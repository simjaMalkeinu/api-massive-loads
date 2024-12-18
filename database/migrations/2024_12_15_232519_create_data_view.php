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
            CREATE 
                ALGORITHM = UNDEFINED 
                DEFINER = root@localhost 
                SQL SECURITY DEFINER
            VIEW data_view AS
                SELECT 
                    p.id AS id,
                    p.nombre AS nombre,
                    p.paterno AS paterno,
                    p.materno AS materno,
                    p.created_at AS persona_created_at,
                    p.updated_at AS persona_updated_at,
                    GROUP_CONCAT(DISTINCT t.numero_telefono
                        ORDER BY t.id ASC
                        SEPARATOR ',') AS telefonos,
                    GROUP_CONCAT(DISTINCT CONCAT(d.calle,
                                ' ',
                                d.numero_exterior,
                                ' ',
                                d.numero_interior,
                                ' ',
                                d.colonia,
                                ' ',
                                d.cp)
                        ORDER BY d.id ASC
                        SEPARATOR ',') AS direcciones
                FROM
                    ((personas p
                    LEFT JOIN telefonos t ON ((p.id = t.persona_id)))
                    LEFT JOIN direcciones d ON ((p.id = d.persona_id)))
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
