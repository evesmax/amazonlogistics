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

    print("--- ALL SALDOS ROWS FOR FABRICANTE 26 ---")
    cursor.execute("SELECT * FROM inventarios_saldos WHERE idfabricante = 26")
    for r in cursor.fetchall():
        print(r)
    print("-" * 80)

    print("--- ALL SALDOS ROWS FOR BODEGA 6 AND PRODUCTO 56 ---")
    cursor.execute("SELECT * FROM inventarios_saldos WHERE idbodega = 6 AND idproducto = 56")
    for r in cursor.fetchall():
        print(r)
    print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
