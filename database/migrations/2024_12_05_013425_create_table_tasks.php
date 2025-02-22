<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['To be Approved','Checked', 'On Progress', 'Finished', 'Cancel'])->default('To be Approved');
            $table->string('ticket_id')->unique();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('cust_account_number')->nullable();
            $table->string('cust_name')->nullable();
            $table->string('cust_type')->nullable();
            $table->string('cus_telephone')->nullable();
            $table->string('cus_email')->nullable();
            $table->text('cus_address')->nullable();
            $table->string('cus_landmark')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
