-- =====================================================
-- MIGRACIÓN DE DATOS EXISTENTES A NUEVA ESTRUCTURA
-- Ejecutar en producción ANTES de php artisan migrate
-- =====================================================
-- Fecha: 2025-08-22
-- Objetivo: Agregar campos nuevos sin perder datos existentes

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- 1. Agregar campos nuevos a pagos
-- =============================================
ALTER TABLE `pagos`
  ADD COLUMN IF NOT EXISTS `pago_parcial` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `abonado`,
  ADD COLUMN IF NOT EXISTS `fecha_vencimiento` date DEFAULT NULL AFTER `fecha_pago`;

-- Índice para consultas de estado + fecha
CREATE INDEX IF NOT EXISTS `pagos_estado_created_at_index` ON `pagos` (`estado`, `created_at`);

-- Convertir costo y abonado a decimal si son float
ALTER TABLE `pagos` MODIFY COLUMN `costo` decimal(12,2) NOT NULL DEFAULT 0.00;
ALTER TABLE `pagos` MODIFY COLUMN `abonado` decimal(12,2) NOT NULL DEFAULT 0.00;

-- =============================================
-- 2. Agregar campos faltantes a cuotas
-- =============================================
-- (cuotas ya tiene la estructura correcta)

-- =============================================
-- 3. Agregar campos faltantes a plan
-- =============================================
ALTER TABLE `plan` MODIFY COLUMN `costo` decimal(12,2) NOT NULL DEFAULT 0.00;

-- =============================================
-- 4. Crear tabla proveedores (si no existe)
-- =============================================
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mb_up` int(11) NOT NULL DEFAULT 0,
  `mb_down` int(11) NOT NULL DEFAULT 0,
  `precio_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `precio_por_mb` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `tipo` enum('internet','equipamiento','ambos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internet',
  `notas` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proveedores_activo_index` (`activo`),
  KEY `proveedores_tipo_index` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. Crear tabla gastos (si no existe)
-- =============================================
CREATE TABLE IF NOT EXISTS `gastos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('cables_utp','herramientas','rj45','routers_clientes','equipos_nodos','fibra_optica','antenas','postes_torres','combustible','salarios','alquiler','servicios','reparaciones','otros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_gasto` date NOT NULL,
  `proveedor` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gastos_user_id_foreign` (`user_id`),
  KEY `gastos_categoria_fecha_gasto_index` (`categoria`, `fecha_gasto`),
  CONSTRAINT `gastos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. Crear tabla audit_logs (si no existe)
-- =============================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `model_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `accion` enum('created','updated','deleted') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valores_anteriores` json DEFAULT NULL,
  `valores_nuevos` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_logs_model_type_model_id_index` (`model_type`, `model_id`),
  KEY `audit_logs_created_at_index` (`created_at`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. Seed: Usuario admin por defecto
-- Password: password (bcrypt hash)
-- =============================================
INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `created_at`, `updated_at`)
VALUES ('Admin', 'admin@admin.com', NOW(), '$2y$12$9UN2sSqQBLpsCSN1YaxI5OFaKSGV6UdNE5i..qX7Vhi5o4pn1P62y', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = 'Admin';

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- FIN DE MIGRACIÓN
-- =====================================================
-- Siguiente paso: php artisan migrate
-- Esto registrará las migraciones en la tabla migrations
-- sin ejecutar los CREATE TABLE (por el Schema::hasTable check)
-- =====================================================
