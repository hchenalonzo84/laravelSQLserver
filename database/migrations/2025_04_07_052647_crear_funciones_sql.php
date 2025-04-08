<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("CREATE OR ALTER FUNCTION protect(@valor VARCHAR(MAX))
            RETURNS VARBINARY(8000)
            AS
            BEGIN
                IF LEN(@valor) < 8 
                    OR PATINDEX('%[A-Z]%', @valor) = 0 
                    OR PATINDEX('%[0-9]%', @valor) = 0 
                    OR PATINDEX('%[!@#$%^&*()_+=<>?/~.-]%', @valor) = 0
                BEGIN
                    RETURN NULL;
                END;
                RETURN ENCRYPTBYPASSPHRASE('Laravel2025$', @valor);
            END;");

        DB::unprepared("CREATE OR ALTER FUNCTION desprotect(@val VARBINARY(8000))
            RETURNS VARCHAR(MAX)
            AS
            BEGIN
                DECLARE @dat VARCHAR(MAX);
                SET @dat = CONVERT(VARCHAR(MAX), DECRYPTBYPASSPHRASE('Laravel2025$', @val));
                IF @dat IS NULL
                    RETURN 'Error: No se pudo desencriptar';
                RETURN @dat;
            END;");

        DB::unprepared("CREATE OR ALTER TRIGGER encriptar_pass
            ON tbUsuarios
            INSTEAD OF INSERT
            AS
            BEGIN
                BEGIN TRY
                    PRINT 'Inicio del TRIGGER';
                    BEGIN TRANSACTION;

                    IF EXISTS (SELECT 1 FROM inserted WHERE DATALENGTH(password)
                        IS NOT NULL AND ISNULL(SQL_VARIANT_PROPERTY(password, 'BaseType'), '') = 'varbinary')
                    BEGIN
                        INSERT INTO tbUsuarios (usuario, password)
                        SELECT usuario, password FROM inserted;

                        PRINT 'Contraseña ya encriptada, insertada correctamente.';
                    END
                    ELSE
                    BEGIN
                        DECLARE @passwordEncriptado VARBINARY(8000);

                        SELECT @passwordEncriptado = dbo.protect(password)
                        FROM inserted;

                        IF @passwordEncriptado IS NULL
                        BEGIN
                            PRINT 'Error: La contraseña no cumple con los requisitos.';
                            ROLLBACK TRANSACTION;
                            RETURN;
                        END

                        INSERT INTO tbUsuarios (usuario, password)
                        SELECT usuario, @passwordEncriptado FROM inserted;

                        PRINT 'Contraseña encriptada e insertada correctamente.';
                    END

                    COMMIT TRANSACTION;
                END TRY
                BEGIN CATCH
                    PRINT 'Error detectado: ' + ERROR_MESSAGE();
                    ROLLBACK TRANSACTION;
                END CATCH;
            END;");

        DB::unprepared("CREATE OR ALTER FUNCTION validar_login (
            @usuario VARCHAR(50),
            @password_ingresado VARCHAR(MAX)
        )
        RETURNS BIT
        AS
        BEGIN
            DECLARE @esValido BIT = 0;
            DECLARE @password_guardado VARBINARY(8000);

            SELECT @password_guardado = password 
            FROM tbUsuarios
            WHERE usuario = @usuario;

            IF @password_guardado IS NOT NULL AND
               CONVERT(VARCHAR(MAX), DECRYPTBYPASSPHRASE('Laravel2025$', @password_guardado)) = @password_ingresado
            BEGIN
                SET @esValido = 1;
            END

            RETURN @esValido;
        END;");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS encriptar_pass;");
        DB::unprepared("DROP FUNCTION IF EXISTS protect;");
        DB::unprepared("DROP FUNCTION IF EXISTS desprotect;");
        DB::unprepared("DROP FUNCTION IF EXISTS validar_login;");
    }
};
