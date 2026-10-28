-- Datos ficticios para refrescar la demo del Club Los Alces.
-- Compatible con la estructura existente de partidos, goles y resultados.
-- Se puede importar más de una vez: no duplica partidos, goles ni resultados.
-- Fechas preparadas para la demo al 28-10-2026.

START TRANSACTION;

-- Reutiliza una cancha presente en la base importada (el ID puede variar).
SET @cancha_demo = (SELECT MIN(`id`) FROM `cancha`);

-- Femenino: Los Alces FC F vs Irreal Madrid, 2-1.
INSERT INTO `partidos` (`equipo_local_fk`, `equipo_visita_fk`, `ubicacion_fk`, `fecha`, `campeonato_id_fk`)
SELECT 10, 11, @cancha_demo, '2026-10-04 15:00:00', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `partidos`
    WHERE `equipo_local_fk` = 10 AND `equipo_visita_fk` = 11
      AND `fecha` = '2026-10-04 15:00:00' AND `campeonato_id_fk` = 1
);
SET @partido_demo_f1 = (
    SELECT `id` FROM `partidos`
    WHERE `equipo_local_fk` = 10 AND `equipo_visita_fk` = 11
      AND `fecha` = '2026-10-04 15:00:00' AND `campeonato_id_fk` = 1 LIMIT 1
);
INSERT INTO `resultados` (`equipo_local_fk`, `equipo_visita_fk`, `goles_local`, `goles_visita`, `id_partido_fk`, `campeonato_id_fk`)
SELECT 10, 11, 2, 1, @partido_demo_f1, 1
WHERE @partido_demo_f1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `resultados` WHERE `id_partido_fk` = @partido_demo_f1);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_f1, 1, 16, NULL
WHERE @partido_demo_f1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_f1 AND `jugador_id_fk` = 1 AND `minuto` = 16);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_f1, 2, 62, NULL
WHERE @partido_demo_f1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_f1 AND `jugador_id_fk` = 2 AND `minuto` = 62);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_f1, NULL, 39, 'Valeria Torres'
WHERE @partido_demo_f1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_f1 AND `jugador_visita` = 'Valeria Torres' AND `minuto` = 39);

-- Masculino: Los Alces FC M vs Rusia Dortmund, 3-2.
INSERT INTO `partidos` (`equipo_local_fk`, `equipo_visita_fk`, `ubicacion_fk`, `fecha`, `campeonato_id_fk`)
SELECT 14, 15, @cancha_demo, '2026-10-11 15:00:00', 2
WHERE NOT EXISTS (
    SELECT 1 FROM `partidos`
    WHERE `equipo_local_fk` = 14 AND `equipo_visita_fk` = 15
      AND `fecha` = '2026-10-11 15:00:00' AND `campeonato_id_fk` = 2
);
SET @partido_demo_m1 = (
    SELECT `id` FROM `partidos`
    WHERE `equipo_local_fk` = 14 AND `equipo_visita_fk` = 15
      AND `fecha` = '2026-10-11 15:00:00' AND `campeonato_id_fk` = 2 LIMIT 1
);
INSERT INTO `resultados` (`equipo_local_fk`, `equipo_visita_fk`, `goles_local`, `goles_visita`, `id_partido_fk`, `campeonato_id_fk`)
SELECT 14, 15, 3, 2, @partido_demo_m1, 2
WHERE @partido_demo_m1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `resultados` WHERE `id_partido_fk` = @partido_demo_m1);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_m1, 20, 11, NULL
WHERE @partido_demo_m1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_m1 AND `jugador_id_fk` = 20 AND `minuto` = 11);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_m1, 21, 58, NULL
WHERE @partido_demo_m1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_m1 AND `jugador_id_fk` = 21 AND `minuto` = 58);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_m1, 26, 77, NULL
WHERE @partido_demo_m1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_m1 AND `jugador_id_fk` = 26 AND `minuto` = 77);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_m1, NULL, 34, 'Matías Soto'
WHERE @partido_demo_m1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_m1 AND `jugador_visita` = 'Matías Soto' AND `minuto` = 34);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_m1, NULL, 65, 'Benjamín Ríos'
WHERE @partido_demo_m1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_m1 AND `jugador_visita` = 'Benjamín Ríos' AND `minuto` = 65);

-- Femenino: Los Alces FC F vs Guachipato, 1-1.
INSERT INTO `partidos` (`equipo_local_fk`, `equipo_visita_fk`, `ubicacion_fk`, `fecha`, `campeonato_id_fk`)
SELECT 10, 12, @cancha_demo, '2026-10-18 12:00:00', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `partidos`
    WHERE `equipo_local_fk` = 10 AND `equipo_visita_fk` = 12
      AND `fecha` = '2026-10-18 12:00:00' AND `campeonato_id_fk` = 1
);
SET @partido_demo_f2 = (
    SELECT `id` FROM `partidos`
    WHERE `equipo_local_fk` = 10 AND `equipo_visita_fk` = 12
      AND `fecha` = '2026-10-18 12:00:00' AND `campeonato_id_fk` = 1 LIMIT 1
);
INSERT INTO `resultados` (`equipo_local_fk`, `equipo_visita_fk`, `goles_local`, `goles_visita`, `id_partido_fk`, `campeonato_id_fk`)
SELECT 10, 12, @cancha_demo, 1, @partido_demo_f2, 1
WHERE @partido_demo_f2 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `resultados` WHERE `id_partido_fk` = @partido_demo_f2);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_f2, 4, 50, NULL
WHERE @partido_demo_f2 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_f2 AND `jugador_id_fk` = 4 AND `minuto` = 50);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_f2, NULL, 72, 'Camila Fuentes'
WHERE @partido_demo_f2 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_f2 AND `jugador_visita` = 'Camila Fuentes' AND `minuto` = 72);

-- Masculino: Los Alces FC M vs Ohiggins Norte, 0-2.
INSERT INTO `partidos` (`equipo_local_fk`, `equipo_visita_fk`, `ubicacion_fk`, `fecha`, `campeonato_id_fk`)
SELECT 14, 16, @cancha_demo, '2026-10-25 15:30:00', 2
WHERE NOT EXISTS (
    SELECT 1 FROM `partidos`
    WHERE `equipo_local_fk` = 14 AND `equipo_visita_fk` = 16
      AND `fecha` = '2026-10-25 15:30:00' AND `campeonato_id_fk` = 2
);
SET @partido_demo_m2 = (
    SELECT `id` FROM `partidos`
    WHERE `equipo_local_fk` = 14 AND `equipo_visita_fk` = 16
      AND `fecha` = '2026-10-25 15:30:00' AND `campeonato_id_fk` = 2 LIMIT 1
);
INSERT INTO `resultados` (`equipo_local_fk`, `equipo_visita_fk`, `goles_local`, `goles_visita`, `id_partido_fk`, `campeonato_id_fk`)
SELECT 14, 16, 0, 2, @partido_demo_m2, 2
WHERE @partido_demo_m2 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `resultados` WHERE `id_partido_fk` = @partido_demo_m2);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_m2, NULL, 24, 'Javier Muñoz'
WHERE @partido_demo_m2 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_m2 AND `jugador_visita` = 'Javier Muñoz' AND `minuto` = 24);
INSERT INTO `goles` (`partido_id_fk`, `jugador_id_fk`, `minuto`, `jugador_visita`)
SELECT @partido_demo_m2, NULL, 83, 'Tomás Herrera'
WHERE @partido_demo_m2 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `goles` WHERE `partido_id_fk` = @partido_demo_m2 AND `jugador_visita` = 'Tomás Herrera' AND `minuto` = 83);

-- Próximos encuentros: se muestran en las pantallas de calendario.
INSERT INTO `partidos` (`equipo_local_fk`, `equipo_visita_fk`, `ubicacion_fk`, `fecha`, `campeonato_id_fk`)
SELECT 10, 1, @cancha_demo, '2026-11-01 15:00:00', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `partidos`
    WHERE `equipo_local_fk` = 10 AND `equipo_visita_fk` = 1
      AND `fecha` = '2026-11-01 15:00:00' AND `campeonato_id_fk` = 1
);
INSERT INTO `partidos` (`equipo_local_fk`, `equipo_visita_fk`, `ubicacion_fk`, `fecha`, `campeonato_id_fk`)
SELECT 14, 18, @cancha_demo, '2026-11-08 12:00:00', 2
WHERE NOT EXISTS (
    SELECT 1 FROM `partidos`
    WHERE `equipo_local_fk` = 14 AND `equipo_visita_fk` = 18
      AND `fecha` = '2026-11-08 12:00:00' AND `campeonato_id_fk` = 2
);

COMMIT;
