<?php

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Schema\Builder;


return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('phone_code')) {
            return;
        }
       
        $schema->create('phone_code', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('phone', 254)->default('');
            $table->string('code',16)->default('');
            $table->string('ip',128)->default('');
            $table->integer('exp_time')->default(0);
            $table->integer('created_time')->default(0);
        });
    },
    'down' => function (Builder $schema) {
        
    },
];