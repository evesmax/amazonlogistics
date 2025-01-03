truncate operaciones_almacenadoras;
INSERT INTO `operaciones_almacenadoras` (`idalmacenadora`, `nombrealmacenadora`, `rfcalmacenadora`, `idestado`, `idmunicipio`, `calle`, `noexterior`, `colonia`, `nointerior`, `telefonos`, `telefonosmoviles`, `correoelectronico`, `paginaweb`, `notasadicionales`, `personaenlace`, `logotipo`)
VALUES
	(1, 'Almacenadora Mercantil Amazon', 'AMA111219AW6', 14, 538, 'ANAXAGORAS', '1329', 'LETRAN VALLE', NULL, '525519623102', NULL, 'ferhernandez@amz-logistics.com.mx', NULL, NULL, NULL, '');


truncate operaciones_bodegas;
INSERT INTO `operaciones_bodegas` (`nombrebodega`, `idestado`, `colonia`, `calle`, `codigopostal`, `responsable`, `idestadocatalogo`)
VALUES
('AMAZON CARTAGENA',15,'Tultitlán de Mariano Escobedo','Av. Dos 84','54918','Responsable Bodega',1),
('AMAZON COATZACOALCOS 2',30,'Colonia Centro','Interior del Recinto Portuario S/N','96400','Responsable Bodega',1),
('AMAZON COATZACOALCOS 6',30,'Colonia Centro','Interior del Recinto Portuario S/N','96400','Responsable Bodega',1),
('AMAZON COATZACOALCOS 8',30,'Colonia Centro','Interior del Recinto Portuario S/N','96400','Responsable Bodega',1),
('AMAZON COATZACOALCOS 9',30,'Colonia Centro','Interior del Recinto Portuario S/N','96400','Responsable Bodega',1),
('AMAZON CUAUTLANCINGO',21,'San Lorenzo Almecatla','Días Ordaz S/N','72710','Responsable Bodega',1),
('AMAZON PARQUE 2000',30,'Parqueadero Industrial 2000','Av. Progreso 267','91808','Responsable Bodega',1),
('AMAZON PARQUE 2001',30,'Parqueadero Industrial 2000','Av. Progreso 267','91808','Responsable Bodega',1),
('AMAZON AMATLAN',30,'Amatlán de los Reyes','Carretera Cordoba - Amatlán 12 de Octubre S/N','94950','Responsable Bodega',1),
('AMAZON AMATLAN "A"',30,'Amatlán de los Reyes','Carretera Cordoba - Amatlán 12 de Octubre S/N','94950','Responsable Bodega',1),
('AMAZON EL QUEMADO',15,'San Juan','Av. 2 de Marzo 120 ','54960','Responsable Bodega',1),
('AMAZON PLATANO Y CACAO NO.31',27,'','','','Responsable Bodega',1),
('AMAZON PLATANO Y CACAO NO.32',27,'','','','Responsable Bodega',1),
('AMAZON PLATANO Y CACAO NO.62',27,'','','','Responsable Bodega',1),
('AMAZON PLATANO Y CACAO NO.65',27,'','','','Responsable Bodega',1),
('AMAZON PLATANO Y CACAO NO.84',27,'','','','Responsable Bodega',1),
('AMAZON PLATANO Y CACAO VII',27,'','','','Responsable Bodega',1),
('AMAZON SANTA INES II',15,'','','','Responsable Bodega',1),
('AMAZON TULTEPEC',15,'','','','Responsable Bodega',1),
('AMAZON VERACRUZ 4',30,'','','','Responsable Bodega',1),
('AMAZON VERACRUZ 7',30,'','','','Responsable Bodega',1);

truncate operaciones_transportistas;
INSERT INTO `operaciones_transportistas` (`idtipopersona`, `razonsocial`,`telefonosfijos`,`idestadocatalogo`)
VALUES
(1,'ABASTECEDORA STA. ROSA, S.A. DE C.V.','271 134 6634',1),
(1,'TRNSPORTES HUAZO','',1),
(1,'ALEJANDRO PIMENTEL JIMENEZ','2727268608',1),
(1,'TRANSBAGO','',1),
(1,'AUTO LINEAS SAN ANTONIO','',1),
(1,'AUTOTRANSPORTES ZONTE S.A DE C.V','',1),
(1,'TRANSPORTES RENTERIA ','',1),
(1,'AUTOEXPRESS HG','',1),
(1,'THEMOSA S.A DE C.V','',1),
(1,'TRANSPORTES FCL','',1),
(1,'IDALIA','',1),
(1,'AMP CARGO S.A DE C.V','',1),
(1,'TRANSPORTES EL OLVIDO','',1),
(1,'TRANSPORTES CARIZUR','',1),
(1,'ALVISAR ','',1),
(1,'TRAPSA','',1),
(1,'TRANSPORTES LAURITA','',1),
(1,'OLIVA','',1),
(1,'AUTOTRANSPORTES ILHUILCAMINA S.A DE C.V','',1),
(1,'EMBOTELLADORA MEXICANA','',1),
(1,'TRANSPORTES BELVA','',1),
(1,'TRANSPORTES DEA','',1),
(1,'TRANPSPORTES G.R.L S.A DE C.V.','',1),
(1,'ADA SARAI','',1),
(1,'TIUSA','',1);


TRUNCATE operaciones_fabricantes;
truncate logistica_cancelacionordenesentrega;
truncate logistica_certificados;
truncate logistica_consecutivosbodega;
truncate logistica_desviosautorizados;
truncate logistica_devoluciones;
truncate logistica_devoluciones_salidas;
truncate logistica_envios;
truncate logistica_faltantestraslados;
truncate logistica_ordenesentrega;
truncate logistica_paroingenios;
truncate logistica_politicas;
truncate logistica_precios_sniim;
truncate logistica_recepciones;
truncate logistica_reservadeproductos;
truncate logistica_retiros;
truncate logistica_tipoendoso;
truncate logistica_tiposenlace;
truncate logistica_tiposoperacion;
truncate logistica_traslados;
truncate relaciones_almacenadoras_bodegas;
truncate relaciones_almacenadoras_bodegas_detalle;
truncate relaciones_ingenios_bodegas;
truncate relaciones_ingenios_bodegas_detalle;
truncate relaciones_ingenios_productos;
truncate relaciones_ingenios_productos_detalle;
truncate relaciones_mercadocontrato;
truncate relaciones_segmentoscontratos;
truncate relaciones_usuariosbodegas;
truncate relaciones_usuariosfabricantes;
truncate inventarios_movimientos;
truncate inventarios_movimientosdetalle;
truncate inventarios_movimientostitulo;
truncate inventarios_saldos;










