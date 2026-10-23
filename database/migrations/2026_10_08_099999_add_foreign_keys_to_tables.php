<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan seluruh foreign key dengan nama constraint persis seperti dump produksi.
     * Dijalankan setelah semua create table dan melewati constraint yang sudah ada.
     */
    public function up(): void
    {
        foreach ($this->definitions() as $table => $constraints) {
            $pending = array_filter($constraints, fn (array $c) => ! $this->constraintExists($table, $c['name']));
            if ($pending === []) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($pending) {
                foreach ($pending as $c) {
                    $blueprint->foreign($c['columns'], $c['name'])
                        ->references($c['ref_columns'])
                        ->on($c['ref_table'])
                        ->onDelete($c['on_delete'])
                        ->onUpdate($c['on_update']);
                }
            });
        }
    }

    /**
     * Hapus seluruh foreign key yang didefinisikan migration ini.
     */
    public function down(): void
    {
        foreach ($this->definitions() as $table => $constraints) {
            $existing = array_filter($constraints, fn (array $c) => $this->constraintExists($table, $c['name']));
            if ($existing === []) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($existing) {
                foreach ($existing as $c) {
                    $blueprint->dropForeign($c['name']);
                }
            });
        }
    }

    private function constraintExists(string $table, string $name): bool
    {
        return DB::selectOne(
            'SELECT 1 AS ok FROM information_schema.table_constraints WHERE constraint_schema = DATABASE() AND table_name = ? AND constraint_name = ?',
            [$table, $name]
        ) !== null;
    }

    private function definitions(): array
    {
        return [
            'article_types' => [
                ['name' => 'fk_warehouse', 'columns' => ['warehouse_id'], 'ref_table' => 'warehouses', 'ref_columns' => ['id'], 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
            ],
            'categories' => [
                ['name' => 'categories_department_id_foreign', 'columns' => ['department_id'], 'ref_table' => 'departments', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
                ['name' => 'categories_user_id_foreign', 'columns' => ['user_id'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'department_user' => [
                ['name' => 'department_user_department_id_foreign', 'columns' => ['department_id'], 'ref_table' => 'departments', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
                ['name' => 'department_user_user_id_foreign', 'columns' => ['user_id'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'document_copies' => [
                ['name' => 'fk_document_copies_taken_from', 'columns' => ['copies_taken_from'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'SET NULL', 'on_update' => 'NO ACTION'],
            ],
            'incoming_inspection_items' => [
                ['name' => 'incoming_inspection_items_incoming_inspection_id_foreign', 'columns' => ['incoming_inspection_id'], 'ref_table' => 'incoming_inspections', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'inspection_defects' => [
                ['name' => 'inspection_defects_inspection_id_foreign', 'columns' => ['inspection_id'], 'ref_table' => 'inspections', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'inspections' => [
                ['name' => 'inspections_user_id_foreign', 'columns' => ['user_id'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'purchase_order_items' => [
                ['name' => 'purchase_order_items_ibfk_2', 'columns' => ['purchase_request_id'], 'ref_table' => 'purchase_request_items', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
                ['name' => 'purchase_order_items_purchase_order_id_foreign', 'columns' => ['purchase_order_id'], 'ref_table' => 'purchase_orders', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            ],
            'purchase_order_request' => [
                ['name' => 'purchase_order_request_purchase_order_id_foreign', 'columns' => ['purchase_order_id'], 'ref_table' => 'purchase_orders', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
                ['name' => 'purchase_order_request_purchase_request_id_foreign', 'columns' => ['purchase_request_id'], 'ref_table' => 'purchase_requests', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'purchase_orders' => [
                ['name' => 'purchase_orders_ibfk_1', 'columns' => ['parent_id'], 'ref_table' => 'purchase_orders', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            ],
            'purchase_request_items' => [
                ['name' => 'purchase_request_items_purchase_request_id_foreign', 'columns' => ['purchase_request_id'], 'ref_table' => 'purchase_requests', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'receiving_items' => [
                ['name' => 'receiving_items_ibfk_1', 'columns' => ['receiving_id'], 'ref_table' => 'receivings', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
                ['name' => 'receiving_items_ibfk_3', 'columns' => ['po_item_id'], 'ref_table' => 'purchase_order_items', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            ],
            'receivings' => [
                ['name' => 'receivings_ibfk_1', 'columns' => ['purchase_order_id'], 'ref_table' => 'purchase_orders', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
                ['name' => 'receivings_ibfk_2', 'columns' => ['supplier_code'], 'ref_table' => 'suppliers', 'ref_columns' => ['code'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            ],
            'role_user' => [
                ['name' => 'role_user_role_id_foreign', 'columns' => ['role_id'], 'ref_table' => 'roles', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
                ['name' => 'role_user_user_id_foreign', 'columns' => ['user_id'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'stocks' => [
                ['name' => 'stocks_ibfk_1', 'columns' => ['warehouse_id'], 'ref_table' => 'warehouses', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            ],
            'suggestion_reward_formula_items' => [
                ['name' => 'suggestion_reward_formula_items_formula_id_foreign', 'columns' => ['formula_id'], 'ref_table' => 'suggestion_reward_formulas', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'suggestion_reward_formulas' => [
                ['name' => 'suggestion_reward_formulas_created_by_foreign', 'columns' => ['created_by'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'suggestion_reward_tiers' => [
                ['name' => 'suggestion_reward_tiers_formula_id_foreign', 'columns' => ['formula_id'], 'ref_table' => 'suggestion_reward_formulas', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'ticket_attachments' => [
                ['name' => 'ticket_attachments_ticket_id_foreign', 'columns' => ['ticket_id'], 'ref_table' => 'tickets', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'ticket_evidences' => [
                ['name' => 'ticket_evidences_ticket_id_foreign', 'columns' => ['ticket_id'], 'ref_table' => 'tickets', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'ticket_holds' => [
                ['name' => 'ticket_holds_created_by_foreign', 'columns' => ['created_by'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
                ['name' => 'ticket_holds_ticket_id_foreign', 'columns' => ['ticket_id'], 'ref_table' => 'tickets', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'tickets' => [
                ['name' => 'tickets_approved_by_foreign', 'columns' => ['approved_by'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'SET NULL', 'on_update' => 'NO ACTION'],
                ['name' => 'tickets_created_by_foreign', 'columns' => ['request_by'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'SET NULL', 'on_update' => 'NO ACTION'],
                ['name' => 'tickets_ibfk_1', 'columns' => ['category_id'], 'ref_table' => 'categories', 'ref_columns' => ['id'], 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
                ['name' => 'tickets_ibfk_2', 'columns' => ['processed_by'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'SET NULL', 'on_update' => 'RESTRICT'],
            ],
            'todos' => [
                ['name' => 'todos_user_id_foreign', 'columns' => ['user_id'], 'ref_table' => 'users', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'transfer_chemical_items' => [
                ['name' => 'transfer_chemical_items_transfer_chemical_id_foreign', 'columns' => ['transfer_chemical_id'], 'ref_table' => 'transfer_chemicals', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'transfer_in' => [
                ['name' => 'transfer_in_from_location_foreign', 'columns' => ['from_location'], 'ref_table' => 'warehouses', 'ref_columns' => ['id'], 'on_delete' => 'SET NULL', 'on_update' => 'NO ACTION'],
            ],
            'transfer_in_items' => [
                ['name' => 'transfer_in_items_ibfk_1', 'columns' => ['destination_id'], 'ref_table' => 'warehouses', 'ref_columns' => ['id'], 'on_delete' => 'SET NULL', 'on_update' => 'CASCADE'],
                ['name' => 'transfer_in_items_ibfk_2', 'columns' => ['transfer_in_id'], 'ref_table' => 'transfer_in', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
                ['name' => 'transfer_in_items_ibfk_3', 'columns' => ['transfer_in_code'], 'ref_table' => 'transfer_in', 'ref_columns' => ['code'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            ],
            'transfer_out_items' => [
                ['name' => 'transfer_out_items_transfer_out_id_foreign', 'columns' => ['transfer_out_id'], 'ref_table' => 'transfer_out', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'NO ACTION'],
            ],
            'users' => [
                ['name' => 'users_ibfk_1', 'columns' => ['role_id'], 'ref_table' => 'roles', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
                ['name' => 'users_ibfk_2', 'columns' => ['department_id'], 'ref_table' => 'departments', 'ref_columns' => ['id'], 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            ],
        ];
    }
};
