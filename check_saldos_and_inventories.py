import pymysql

DB_HOST = "34.66.63.218"
DB_USER = "nmdevel"
DB_PASS = "nmdevel"
DB_NAME = "_dbmlog0000018677"

def main():
    conn = pymysql.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME
    )
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    # Combinations affected:
    # 1. Tultepec (bodega 6), LanRam (fabricante 26, marca 40), Producto 56, Estado 1
    # 2. Purificacion (bodega 12), Santa Rosalia (fabricante 2, marca 2), Producto 57, Estado 1
    # 3. Amatlan (bodega 1), San Miguelito (fabricante 20, marca 20), Producto 56, Estado 4
    
    combinations = [
        {"bodega": 6, "fabricante": 26, "marca": 40, "producto": 56, "estado": 1, "desc": "Tultepec - LanRam - Prod 56 - Est 1"},
        {"bodega": 12, "fabricante": 2, "marca": 2, "producto": 57, "estado": 1, "desc": "Purificación - Santa Rosalia - Prod 57 - Est 1"},
        {"bodega": 1, "fabricante": 20, "marca": 20, "producto": 56, "estado": 4, "desc": "Amatlán - San Miguelito - Prod 56 - Est 4"}
    ]

    for c in combinations:
        print(f"=== {c['desc']} ===")
        # Get current balance from inventarios_saldos
        sql_saldo = """
            SELECT entradas, salidas, saldo, entradassecundario, salidassecundario, saldosecundario
            FROM inventarios_saldos
            WHERE idfabricante = %s AND idmarca = %s AND idbodega = %s AND idproducto = %s AND idestadoproducto = %s
        """
        cursor.execute(sql_saldo, [c['fabricante'], c['marca'], c['bodega'], c['producto'], c['estado']])
        saldo_row = cursor.fetchone()
        
        # Get sum of movements (excluding the missing ones since they are not in DB right now)
        # Wait, what are the types of movements?
        # Let's check sum of all movements for this combination
        sql_movs_sum = """
            SELECT 
                SUM(CASE WHEN m.idtipomovimiento IN (SELECT idtipomovimiento FROM inventarios_tiposmovimiento WHERE efectoinventario = 1) THEN cantidad ELSE 0 END) as sum_in,
                SUM(CASE WHEN m.idtipomovimiento IN (SELECT idtipomovimiento FROM inventarios_tiposmovimiento WHERE efectoinventario = -1) THEN cantidad ELSE 0 END) as sum_out,
                SUM(CASE WHEN m.idtipomovimiento IN (SELECT idtipomovimiento FROM inventarios_tiposmovimiento WHERE efectoinventario = 1) THEN cantidadsecundaria ELSE 0 END) as sum_in_sec,
                SUM(CASE WHEN m.idtipomovimiento IN (SELECT idtipomovimiento FROM inventarios_tiposmovimiento WHERE efectoinventario = -1) THEN cantidadsecundaria ELSE 0 END) as sum_out_sec
            FROM inventarios_movimientos m
            WHERE m.idfabricante = %s AND m.idmarca = %s AND m.idbodega = %s AND m.idproducto = %s AND m.idestadoproducto = %s
        """
        cursor.execute(sql_movs_sum, [c['fabricante'], c['marca'], c['bodega'], c['producto'], c['estado']])
        mov_sum = cursor.fetchone()
        
        print("Saldos Table:")
        print(f"  Entradas: {saldo_row['entradas'] if saldo_row else 0} | Salidas: {saldo_row['salidas'] if saldo_row else 0} | Saldo: {saldo_row['saldo'] if saldo_row else 0}")
        print(f"  Entradas Sec: {saldo_row['entradassecundario'] if saldo_row else 0} | Salidas Sec: {saldo_row['salidassecundario'] if saldo_row else 0} | Saldo Sec: {saldo_row['saldosecundario'] if saldo_row else 0}")
        
        print("Movements Sum (Current DB):")
        sum_in = mov_sum['sum_in'] or 0
        sum_out = mov_sum['sum_out'] or 0
        sum_in_sec = mov_sum['sum_in_sec'] or 0
        sum_out_sec = mov_sum['sum_out_sec'] or 0
        print(f"  Entradas: {sum_in} | Salidas: {sum_out} | Saldo: {sum_in - sum_out}")
        print(f"  Entradas Sec: {sum_in_sec} | Salidas Sec: {sum_out_sec} | Saldo Sec: {sum_in_sec - sum_out_sec}")
        print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
