truncate operaciones_almacenadoras;
INSERT INTO `operaciones_almacenadoras` (`idalmacenadora`, `nombrealmacenadora`, `rfcalmacenadora`, `idestado`, `idmunicipio`, `calle`, `noexterior`, `colonia`, `nointerior`, `telefonos`, `telefonosmoviles`, `correoelectronico`, `paginaweb`, `notasadicionales`, `personaenlace`, `logotipo`)
VALUES
	(1, 'Almacenadora Mercantil Amazon', 'AMA111219AW6', 14, 538, 'ANAXAGORAS', '1329', 'LETRAN VALLE', NULL, '525575742291', NULL, 'ferhernandez@amz-logistics.com.mx', NULL, NULL, NULL, '');


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

truncate operaciones_fabricantes;


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
truncate inventarios_movimientos;
truncate inventarios_movimientosdetalle;
truncate inventarios_movimientostitulo;
truncate inventarios_saldos;




select of.idfabricante, vm.idmarca, ip.idproducto,il.idloteproducto,ob.idbodega,
                                re.saldosecundario 'existenciafisica',
                                re.entradassecundario 'entradasacumuladas', re.salidassecundario 'salidasacumuladas',
                                (
                    select sum(md.cantidadsecundaria) from inventarios_movimientos md
                        left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=md.idtipomovimiento
                    where tm.efectoinventario=1
                        and md.idfabricante=re.idfabricante
						and md.idmarca=re.idmarca
                        and md.idbodega=re.idbodega
                        and md.idproducto=re.idproducto
                        and md.idloteproducto=re.idloteproducto
                        and md.idestadoproducto=re.idestadoproducto
                        and md.fecha between '2025-01-03 00:00:00' and '2025-01-03 23:59:59'
                    ) entradasdeldia,
                                (
                    select sum(md.cantidadsecundaria) from inventarios_movimientos md
                        left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=md.idtipomovimiento
                    where tm.efectoinventario=-1
                        and md.idfabricante=re.idfabricante
						and md.idmarca=re.idmarca
                        and md.idbodega=re.idbodega
                        and md.idproducto=re.idproducto
                        and md.idloteproducto=re.idloteproducto
                        and md.idestadoproducto=re.idestadoproducto
                        and md.fecha between '2025-01-03 00:00:00' and '2025-01-03 23:59:59'
                    ) salidasdeldia,
                                ie.idestadoproducto,ip.idfamilia
                        from inventarios_saldos re
                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
                            left join vista_marcas vm on vm.idmarca=re.idmarca
                            left join inventarios_productos ip on ip.idproducto=re.idproducto
                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                            left join operaciones_bodegas ob on ob.idbodega =re.idbodega
                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia where (re.idbodega in (select idbodega from relaciones_usuariosbodegas where idempleado=2) 
 OR NOT EXISTS (SELECT 1 FROM relaciones_usuariosbodegas WHERE idempleado=2)) and
(of.nombrefabricante like "%%") and (vm.nombremarca like "%%") 
and (ifa.nombrefamilia like "%%") and (ip.nombreproducto like "%%") 
and (ie.descripcionestado like "%%") and (il.descripcionlote like "%%") 
and (ob.nombrebodega like "%%")   order by of.nombrefabricante, ip.nombreproducto, ie.idestadoproducto, il.descripcionlote, ob.nombrebodega 



select * from inventarios_saldos
select * from inventarios_movimientos
