<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::beginTransaction();

            // Step 1: Copy data from customers to parties table
            DB::statement("
                delete from parties
            ");

            DB::statement("
                INSERT INTO parties (
                    id,
                    party_type,
                    first_name, 
                    last_name, 
                    email, 
                    mobile, 
                    whatsapp, 
                    billing_address, 
                    created_by, 
                    updated_by, 
                    created_at, 
                    updated_at, 
                    status
                )
                SELECT 
                id,
                'customer',
                    first_name, 
                    last_name, 
                    email, 
                    mobile, 
                    whatsapp, 
                    address, 
                    created_by, 
                    updated_by, 
                    created_at, 
                    updated_at, 
                    status
                FROM customers
            ");

            //Step 2: Add party_id column to orders table
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('party_id')->nullable()->after('customer_id');
            });

            // Step 3: Copy customer_id values to party_id
            // Step 3: Copy customer_id values to party_id
            DB::statement("
                UPDATE orders o 
                SET party_id = (
                    SELECT p.id 
                    FROM customers c 
                    INNER JOIN parties p ON p.email = c.email AND p.mobile = c.mobile 
                    WHERE c.id = o.customer_id
                    LIMIT 1
                )
            ");

            // Step 4: Add foreign key to party_id
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('party_id')
                      ->references('id')
                      ->on('parties')
                      ->onDelete('cascade');
            });

            // Step 5: Make party_id not nullable after data is copied
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('party_id')->nullable(false)->change();
            });

            // Step 6: Drop the old foreign key if exists
            $constraintExists = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.key_column_usage
                WHERE table_schema = DATABASE()
                AND table_name = 'orders'
                AND column_name = 'customer_id'
                AND referenced_table_name IS NOT NULL
            ");

            if ($constraintExists[0]->count > 0) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropForeign(['customer_id']);
                });
            }

            // Step 7: Drop the customer_id column
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('customer_id');
            });

            //DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Migration failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function down(): void
    {
        //
    }
};
