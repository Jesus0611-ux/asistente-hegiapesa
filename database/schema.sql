-- =============================================================================
-- HegiaStock — Esquema de Base de Datos
-- Motor    : MariaDB / MySQL (XAMPP)
-- Encoding : utf8mb4 / utf8mb4_unicode_ci
-- Versión  : 1.0 · Octubre 2026
-- Referencia: docs/arquitectura/modelo_datos.md
--
-- RESTRICCIÓN (Ley N.° 29733): Ninguna tabla contiene datos personales de
-- clientes finales. Solo se almacenan datos operativos de personal interno.
--
-- INSTRUCCIONES DE EJECUCIÓN EN phpMyAdmin:
--   1. Abrir http://localhost/phpmyadmin
--   2. Pestaña "SQL" en la pantalla inicial (sin base de datos seleccionada)
--   3. Pegar este script completo y ejecutar
--   4. Verificar que aparezcan las 9 tablas bajo la base de datos hegiastock
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Base de datos
-- -----------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS hegiastock
    CHARACTER SET  utf8mb4
    COLLATE        utf8mb4_unicode_ci;

USE hegiastock;

-- -----------------------------------------------------------------------------
-- 1. rol
--    Catálogo de roles del personal interno.
--    Componente propietario: RepositorioUsuarios / Modelos ORM
--    Historias: HU07 (control de acceso y protección de datos)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rol (
    id_rol      TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nombre_rol  VARCHAR(50)         NOT NULL,
    descripcion VARCHAR(200)        NULL,
    CONSTRAINT pk_rol PRIMARY KEY (id_rol),
    CONSTRAINT uq_rol_nombre UNIQUE (nombre_rol)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de roles del personal interno. Propietario: RepositorioUsuarios';

-- -----------------------------------------------------------------------------
-- 2. usuario
--    Personal interno operativo (Vendedor, Almacenero, Administrador).
--    NOTA PDP: nombre_usuario es un alias operativo (ej. vendedor_01),
--    NO nombre real ni dato personal del cliente.
--    Componente propietario: RepositorioUsuarios / Modelos ORM
--    Historias: HU05 (solicitante del ticket), HU07 (roles sin PDP de clientes)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario      SMALLINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    nombre_usuario  VARCHAR(60)         NOT NULL
                        COMMENT 'Alias operativo de sistema, no nombre real (Ley 29733)',
    hash_contrasena VARCHAR(255)        NOT NULL
                        COMMENT 'Hash bcrypt; nunca texto claro',
    id_rol          TINYINT UNSIGNED    NOT NULL,
    activo          BOOLEAN             NOT NULL DEFAULT TRUE,
    creado_en       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_usuario  PRIMARY KEY (id_usuario),
    CONSTRAINT uq_usuario_nombre UNIQUE (nombre_usuario),
    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Personal interno operativo. Sin datos personales de clientes. Propietario: RepositorioUsuarios';

CREATE INDEX idx_usuario_rol ON usuario (id_rol);

-- -----------------------------------------------------------------------------
-- 3. categoria
--    Agrupación lógica de productos del catálogo.
--    Componente propietario: RepositorioCategoria / ServicioCategoria
--    Historias: HU03 (filtrar productos por categoría)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categoria (
    id_categoria SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(80)       NOT NULL,
    descripcion  VARCHAR(200)      NULL,
    activo       BOOLEAN           NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_categoria PRIMARY KEY (id_categoria),
    CONSTRAINT uq_categoria_nombre UNIQUE (nombre)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Agrupación lógica de productos. Propietario: RepositorioCategoria';

CREATE INDEX idx_categoria_nombre ON categoria (nombre);

-- -----------------------------------------------------------------------------
-- 4. unidad_medida
--    Catálogo de unidades de medida (und, kg, m, l, etc.).
--    Componente propietario: RepositorioStock / ServicioStock
--    Historias: HU01 (mostrar cantidad con unidad), HU03 (idem en listado)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS unidad_medida (
    id_unidad   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    simbolo     VARCHAR(20)      NOT NULL,
    descripcion VARCHAR(60)      NULL,
    CONSTRAINT pk_unidad_medida PRIMARY KEY (id_unidad),
    CONSTRAINT uq_unidad_simbolo UNIQUE (simbolo)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de unidades de medida. Propietario: RepositorioStock';

-- -----------------------------------------------------------------------------
-- 5. producto
--    Artículo del catálogo identificado por SKU y nombre comercial.
--    Componente propietario: RepositorioStock / ServicioStock
--    Historias: HU01 (consulta por SKU o nombre), HU02 (base para ubicación),
--               HU03 (listado por categoría), HU05 (validación de SKU en ticket)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS producto (
    id_producto         INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    sku                 VARCHAR(50)         NOT NULL,
    nombre_comercial    VARCHAR(150)        NOT NULL,
    id_categoria        SMALLINT UNSIGNED   NOT NULL,
    id_unidad           TINYINT UNSIGNED    NOT NULL,
    cantidad_disponible INT                 NOT NULL DEFAULT 0
                            CHECK (cantidad_disponible >= 0),
    activo              BOOLEAN             NOT NULL DEFAULT TRUE,
    actualizado_en      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_producto PRIMARY KEY (id_producto),
    CONSTRAINT uq_producto_sku UNIQUE (sku),
    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_producto_unidad
        FOREIGN KEY (id_unidad) REFERENCES unidad_medida(id_unidad)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de productos del inventario. Propietario: RepositorioStock';

CREATE INDEX idx_producto_sku          ON producto (sku);
CREATE INDEX idx_producto_nombre       ON producto (nombre_comercial);
CREATE INDEX idx_producto_categoria    ON producto (id_categoria);
CREATE INDEX idx_producto_unidad       ON producto (id_unidad);
CREATE INDEX idx_producto_cantidad     ON producto (cantidad_disponible);

-- -----------------------------------------------------------------------------
-- 6. ubicacion
--    Posición física de un producto: pasillo y estante.
--    Relación 1:1 con producto (un producto → una ubicación registrada).
--    Componente propietario: RepositorioUbicacion / ServicioUbicacion
--    Historias: HU02 (consultar pasillo y estante exactos)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ubicacion (
    id_ubicacion    INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    id_producto     INT UNSIGNED        NOT NULL,
    numero_pasillo  TINYINT UNSIGNED    NOT NULL
                        CHECK (numero_pasillo >= 1),
    codigo_estante  VARCHAR(10)         NOT NULL,
    observacion     VARCHAR(100)        NULL,
    CONSTRAINT pk_ubicacion PRIMARY KEY (id_ubicacion),
    CONSTRAINT uq_ubicacion_producto UNIQUE (id_producto),
    CONSTRAINT fk_ubicacion_producto
        FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Ubicación física (pasillo/estante) de cada producto. Propietario: RepositorioUbicacion';

CREATE INDEX idx_ubicacion_producto ON ubicacion (id_producto);
CREATE INDEX idx_ubicacion_pasillo  ON ubicacion (numero_pasillo);

-- -----------------------------------------------------------------------------
-- 7. estado_ticket
--    Catálogo de estados posibles de un ticket de verificación.
--    Valores iniciales sugeridos: Pendiente, En Verificación, Cerrado.
--    Componente propietario: RepositorioTickets / ServicioTickets
--    Historias: HU05 (estados del ciclo de vida del ticket VER-NNN)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS estado_ticket (
    id_estado    TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre_estado VARCHAR(30)    NOT NULL,
    CONSTRAINT pk_estado_ticket PRIMARY KEY (id_estado),
    CONSTRAINT uq_estado_nombre UNIQUE (nombre_estado)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de estados del ticket de verificación. Propietario: RepositorioTickets';

-- -----------------------------------------------------------------------------
-- 8. ticket_verificacion
--    Solicitud formal de constatación física de un producto con discrepancia.
--    El correlativo VER-NNN se genera en la capa de servicio (ServicioTickets)
--    con BEGIN IMMEDIATE para garantizar unicidad; aquí solo se persiste.
--    Componente propietario: RepositorioTickets / ServicioTickets
--    Historias: HU05 (registro VER-NNN, estado Pendiente, sin PDP de clientes)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ticket_verificacion (
    id_ticket               INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    numero_correlativo      SMALLINT UNSIGNED   NOT NULL
                                COMMENT 'Valor numérico del correlativo (ej. 1 para VER-001); el prefijo VER- se aplica en la capa de servicio',
    sku_referencia          VARCHAR(50)         NOT NULL
                                COMMENT 'SKU del producto con discrepancia; sin datos personales del cliente',
    id_estado               TINYINT UNSIGNED    NOT NULL,
    id_usuario_solicitante  SMALLINT UNSIGNED   NOT NULL
                                COMMENT 'Personal interno (Vendedor) que abre el ticket; no es dato del cliente final',
    creado_en               DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_ticket PRIMARY KEY (id_ticket),
    CONSTRAINT uq_ticket_correlativo UNIQUE (numero_correlativo),
    CONSTRAINT fk_ticket_estado
        FOREIGN KEY (id_estado) REFERENCES estado_ticket(id_estado)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ticket_usuario
        FOREIGN KEY (id_usuario_solicitante) REFERENCES usuario(id_usuario)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Solicitudes de verificación física VER-NNN. Sin datos personales de clientes. Propietario: RepositorioTickets';

CREATE INDEX idx_ticket_estado    ON ticket_verificacion (id_estado);
CREATE INDEX idx_ticket_sku       ON ticket_verificacion (sku_referencia);
CREATE INDEX idx_ticket_usuario   ON ticket_verificacion (id_usuario_solicitante);
CREATE INDEX idx_ticket_creado    ON ticket_verificacion (creado_en);

-- -----------------------------------------------------------------------------
-- 9. audit_log
--    Registro de eventos del sistema sanitizado por FiltroPDP antes de
--    persistir. No contiene datos personales de clientes.
--    Componente propietario: FiltroPDP / Modelos ORM
--    Historias: HU07 (trazabilidad sin PDP), HU08 (log de operaciones)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_log (
    id_log              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    entidad             VARCHAR(80)     NOT NULL
                            COMMENT 'Nombre de la tabla afectada',
    id_entidad_afectada INT UNSIGNED    NULL
                            COMMENT 'PK de la fila afectada (NULL si es evento de sistema)',
    accion              VARCHAR(30)     NOT NULL
                            CHECK (accion IN ('SELECT','INSERT','UPDATE','DELETE','ERROR','LOGIN','LOGOUT')),
    detalle_sanitizado  VARCHAR(500)    NULL
                            COMMENT 'Texto libre procesado por FiltroPDP; patrones PDP reemplazados por [DATO_PERSONAL]',
    ocurrido_en         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_audit_log PRIMARY KEY (id_log)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de eventos sanitizado. Procesado por FiltroPDP antes de persistir. Propietario: FiltroPDP / Modelos ORM';

CREATE INDEX idx_audit_entidad    ON audit_log (entidad);
CREATE INDEX idx_audit_accion     ON audit_log (accion);
CREATE INDEX idx_audit_ocurrido   ON audit_log (ocurrido_en);

-- =============================================================================
-- Fin del script
-- Verificación: SELECT table_name FROM information_schema.tables
--               WHERE table_schema = 'hegiastock' ORDER BY table_name;
-- Resultado esperado: 9 tablas en el orden:
--   audit_log, categoria, estado_ticket, producto, rol,
--   ticket_verificacion, ubicacion, unidad_medida, usuario
-- =============================================================================
