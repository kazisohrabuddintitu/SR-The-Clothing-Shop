<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->foreignKeyExists('cart_items', 'cart_items_cart_id_foreign')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropForeign('cart_items_cart_id_foreign');
            });
        }

        if ($this->foreignKeyExists('cart_items', 'cart_items_product_id_foreign')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropForeign('cart_items_product_id_foreign');
            });
        }

        if (! Schema::hasColumn('cart_items', 'size')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->string('size')->default('M')->after('product_id');
            });
        }

        if ($this->indexExists('cart_items', 'cart_items_cart_id_product_id_unique')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropUnique('cart_items_cart_id_product_id_unique');
            });
        }

        if (! $this->indexExists('cart_items', 'cart_items_cart_id_product_id_size_unique')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->unique(['cart_id', 'product_id', 'size']);
            });
        }

        if (! $this->foreignKeyExists('cart_items', 'cart_items_cart_id_foreign')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            });
        }

        if (! $this->foreignKeyExists('cart_items', 'cart_items_product_id_foreign')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if ($this->foreignKeyExists('cart_items', 'cart_items_cart_id_foreign')) {
                $table->dropForeign('cart_items_cart_id_foreign');
            }
            if ($this->foreignKeyExists('cart_items', 'cart_items_product_id_foreign')) {
                $table->dropForeign('cart_items_product_id_foreign');
            }
            if ($this->indexExists('cart_items', 'cart_items_cart_id_product_id_size_unique')) {
                $table->dropUnique(['cart_id', 'product_id', 'size']);
            }
            if (! $this->indexExists('cart_items', 'cart_items_cart_id_product_id_unique')) {
                $table->unique(['cart_id', 'product_id']);
            }
            if (Schema::hasColumn('cart_items', 'size')) {
                $table->dropColumn('size');
            }
            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    private function foreignKeyExists(string $table, string $key): bool
    {
        return count(DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $key]
        )) > 0;
    }

    private function indexExists(string $table, string $index): bool
    {
        return count(DB::select(
            'SHOW INDEX FROM '.$table.' WHERE Key_name = ?',
            [$index]
        )) > 0;
    }
};
