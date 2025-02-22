<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('cust_account_number')->nullable();
            $table->string('cust_name')->nullable();
            $table->string('cust_type')->nullable();
            $table->string('cus_telephone')->nullable();
            $table->string('cus_email')->nullable();
            $table->text('cus_address')->nullable();
            $table->string('cus_landmark')->nullable();
        });

        Schema::table('team_tasks', function (Blueprint $table) {
            $table->string('cust_account_number')->nullable();
            $table->string('cust_name')->nullable();
            $table->string('cust_type')->nullable();
            $table->string('cus_telephone')->nullable();
            $table->string('cus_email')->nullable();
            $table->text('cus_address')->nullable();
            $table->string('cus_landmark')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'cust_account_number', 'cust_name', 'cust_type', 'cus_telephone', 'cus_email', 'cus_address', 'cus_landmark'
            ]);
        });

        Schema::table('team_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'cust_account_number', 'cust_name', 'cust_type', 'cus_telephone', 'cus_email', 'cus_address', 'cus_landmark'
            ]);
        });
    }
};
