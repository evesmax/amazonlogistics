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

    print("--- INVENTARIOS_MOVIMIENTOS FOR FABRICANTE 26, BODEGA 6, PRODUCTO 56 ---")
    sql = """
        SELECT idmarca, COUNT(*) as count, SUM(cantidad) as sum_qty, SUM(cantidadsecundaria) as sum_qty_sec
        FROM inventarios_movimientos
        WHERE idfabricante = 26 AND idbodega = 6 AND idproducto = 56
        GROUP BY idmarca
    """
    cursor.execute(sql)
    for r in cursor.fetchall():
        print(r)
    print()

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
