<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbUsuarios', function (Blueprint $table) {
            $table->id('idUsuario');            
            $table->string('usuario',50);
            $table->binary('password');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE tbUsuarios ALTER COLUMN password VARBINARY(8000) NOT NULL");
        DB::statement("ALTER TABLE tbUsuarios ADD CONSTRAINT UQ_usuario UNIQUE(usuario)");
        DB::statement("ALTER TABLE tbUsuarios ADD CONSTRAINT DF_tbUsuarios_created DEFAULT GETDATE() FOR created_at");
        DB::statement("ALTER TABLE tbUsuarios ADD CONSTRAINT DF_tbUsuarios_updated DEFAULT GETDATE() FOR updated_at");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbUsuarios');
    }
};
