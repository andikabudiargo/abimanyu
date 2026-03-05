<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateArticlePricing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly ?string $articleCode = null
    ) {}

    public function handle(): void
    {
        $conversion = DB::table('conversion_values')
            ->where('effective_date', '<=', date('Y-m-d'))
            ->orderByDesc('effective_date')
            ->value('value');

        // Fallback jika conversion null atau 0
        $conv = $conversion > 0 ? (float) $conversion : null;

        $query = DB::table('articles as a')
            ->selectRaw("
                a.article_code,
                avg_rm.average_raw_material_price,
                sj.selling_price,

                CASE
                    WHEN ? > 0
                    THEN avg_rm.average_raw_material_price / ?
                    ELSE NULL
                END as rm_conversion,

                CASE
                    WHEN ? > 0
                    THEN sj.selling_price / ?
                    ELSE NULL
                END as fg_conversion,

                CASE
                    WHEN ? > 0
                    THEN (sj.selling_price / ?) - (avg_rm.average_raw_material_price / ?)
                    ELSE NULL
                END as matome,

                ? as conversion_value_used
            ", [
                $conv, $conv,   // rm_conversion
                $conv, $conv,   // fg_conversion
                $conv, $conv, $conv,  // matome
                $conv,          // conversion_value_used
            ])

            ->leftJoin(DB::raw("
                (
                    SELECT
                        b.article_fg,
                        AVG(seg.segment_avg) as average_raw_material_price
                    FROM boms b
                    INNER JOIN (
                        SELECT
                            article_code,
                            grp,
                            AVG(price) as segment_avg
                        FROM (
                            SELECT
                                article_code,
                                price,
                                @grp := IF(
                                    @prev_price <> price OR @prev_article <> article_code,
                                    @grp + 1,
                                    @grp
                                ) as grp,
                                @prev_price    := price,
                                @prev_article  := article_code
                            FROM lpb_temporary
                            CROSS JOIN (
                                SELECT
                                    @grp          := 0,
                                    @prev_price   := NULL,
                                    @prev_article := NULL
                            ) init
                            ORDER BY article_code, id
                        ) flagged
                        GROUP BY article_code, grp
                    ) seg ON seg.article_code = b.article_rm
                    GROUP BY b.article_fg
                ) avg_rm
            "), 'avg_rm.article_fg', '=', 'a.article_code')

            ->leftJoin(DB::raw("
                (
                    SELECT
                        article_code,
                        (price + service_price) as selling_price
                    FROM sj_temporary
                ) sj
            "), 'sj.article_code', '=', 'a.article_code')

            ->whereRaw("a.article_type = 'FG'")
            ->whereRaw("a.status = 'active'");

        if ($this->articleCode) {
            $query->where('a.article_code', $this->articleCode);
        }

        $rows = $query->get();

        foreach ($rows as $row) {
            DB::table('article_pricing_cache')->updateOrInsert(
                [
                    'article_code' => $row->article_code,
                ],
                [
                    'purchase_price' => $row->average_raw_material_price,
                    'selling_price'              => $row->selling_price,
                    'rm_conversion'              => $row->rm_conversion,
                    'fg_conversion'              => $row->fg_conversion,
                    'matome'                     => $row->matome,
                    'conversion_value_used'      => $row->conversion_value_used,
                    'last_calculated_at'         => now(),
                ]
            );
        }
    }
}