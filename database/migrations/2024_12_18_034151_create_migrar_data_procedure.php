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
        DB::statement(
            'CREATE DEFINER=root@localhost PROCEDURE MigrarDatosPersonas()
                BEGIN
                    DECLARE done INT DEFAULT 0;
                    DECLARE v_nombre VARCHAR(255);
                    DECLARE v_paterno VARCHAR(255);
                    DECLARE v_materno VARCHAR(255);
                    DECLARE v_id_persona INT;
                    DECLARE id_tmp INT;

                    -- Cursor para recorrer los datos de la tabla temporal
                    DECLARE persona_cursor CURSOR FOR
                        SELECT DISTINCT id, nombre, paterno, materno
                        FROM tmp_data;

                    -- Handler para el cursor
                    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                    -- Abrir cursor
                    OPEN persona_cursor;

                    -- Leer el primer registro
                    FETCH persona_cursor INTO id_tmp, v_nombre, v_paterno, v_materno;

                    -- Iterar sobre los registros del cursor
                    WHILE done = 0 DO
                        -- Verificar si la persona ya existe
                        IF NOT EXISTS (
                            SELECT 1
                            FROM personas
                            WHERE nombre = v_nombre
                            AND paterno = v_paterno
                            AND materno = v_materno
                        ) THEN
                            -- Insertar persona si no existe
                            INSERT INTO personas (nombre, paterno, materno, created_at, updated_at)
                            VALUES (v_nombre, v_paterno, v_materno, NOW(), NOW());

                            -- Obtener el ID de la persona recién insertada
                            SET v_id_persona = LAST_INSERT_ID();
                        ELSE
                            -- Obtener el ID de la persona existente
                            SELECT id INTO v_id_persona
                            FROM personas
                            WHERE nombre = v_nombre AND paterno = v_paterno AND materno = v_materno;
                        END IF;
                        
                        -- Verificar si el teléfono ya existe y insertarlo si no
                        IF NOT EXISTS (
                            SELECT 1 FROM telefonos WHERE persona_id = v_id_persona AND numero_telefono = (SELECT telefono FROM tmp_data WHERE id = id_tmp)
                        ) THEN
                            -- Insertar teléfonos asociados a la persona
                            INSERT INTO telefonos (persona_id, numero_telefono, created_at, updated_at)
                            SELECT v_id_persona, telefono, NOW(), NOW()
                            FROM tmp_data
                            WHERE id = id_tmp AND telefono IS NOT NULL;
                        END IF;

                        IF NOT EXISTS (
                            SELECT 1 
                            FROM direcciones 
                            WHERE persona_id = v_id_persona
                            AND calle = (SELECT calle FROM tmp_data WHERE id = id_tmp)
                            AND numero_exterior = (SELECT numero_exterior FROM tmp_data WHERE id = id_tmp)
                            AND numero_interior = (SELECT numero_interior FROM tmp_data WHERE id = id_tmp)
                            AND colonia = (SELECT colonia FROM tmp_data WHERE id = id_tmp)
                            AND cp = (SELECT cp FROM tmp_data WHERE id = id_tmp)
                        ) THEN
                            -- Insertar direcciones asociadas a la persona
                            INSERT INTO direcciones (persona_id, calle, numero_exterior, numero_interior, colonia, cp, created_at, updated_at)
                            SELECT v_id_persona, calle, numero_exterior, numero_interior, colonia, cp, NOW(), NOW()
                            FROM tmp_data
                            WHERE id = id_tmp
                            AND calle IS NOT NULL
                            AND colonia IS NOT NULL
                            AND cp IS NOT NULL;
                        END IF;
                        
                        -- Eliminar el registro procesado de la tabla temporal
                        DELETE FROM tmp_data WHERE id = id_tmp;

                        -- Leer el siguiente registro
                        FETCH persona_cursor INTO id_tmp, v_nombre, v_paterno, v_materno;
                    END WHILE;

                    -- Cerrar el cursor
                    CLOSE persona_cursor;

                END'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS MigrarDatosPersonas');
    }
};
