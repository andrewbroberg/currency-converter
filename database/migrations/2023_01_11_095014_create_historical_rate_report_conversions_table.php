<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Symfony\Component\Translation\t;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_rate_report_conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('historical_rate_report_id');
            $table->date('date');
            $table->char('source', 3);
            $table->char('currency', 3);
            $table->unsignedFloat('conversion_rate');
            $table->timestamps();

            $table->foreign('historical_rate_report_id')->references('id')->on('historical_rate_reports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_rate_report_conversions');
    }
};
