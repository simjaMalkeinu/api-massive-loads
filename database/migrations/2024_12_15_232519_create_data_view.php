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
        DB::statement("
            CREATE VIEW data_view AS
            SELECT 
                p.*, 
                JSON_ARRAYAGG(t.numero_telefono) AS telefonos,
                JSON_ARRAYAGG(CONCAT(d.calle, ' ', d.numero_exterior, ' ', IFNULL(d.numero_interior, ''), ' ', d.codigo_postal)) AS direcciones
            FROM personas p
            LEFT JOIN telefonos t ON p.id = t.persona_id
            LEFT JOIN direcciones d ON p.id = d.persona_id
            GROUP BY p.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS data_view');
    }
};
