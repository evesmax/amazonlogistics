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

    combinations = [
        {"bodega": 6, "fabricante": 26, "marca": 40, "producto": 56, "estado": 1, "desc": "Tultepec - LanRam"},
        {"bodega": 12, "fabricante": 2, "marca": 2, "producto": 57, "estado": 1, "desc": "Purificación - Santa Rosalia"},
        {"bodega": 1, "fabricante": 20, "marca": 20, "producto": 56, "estado": 4, "desc": "Amatlán - San Miguelito"}
    ]

    for c in combinations:
        print(f"=== {c['desc']} ===")
        sql = """
            SELECT * FROM inventarios_saldos
            WHERE idfabricante = %s AND idmarca = %s AND idbodega = %s AND idproducto = %s AND idestadoproducto = %s
        """
        cursor.execute(sql, [c['fabricante'], c['marca'], c['bodega'], c['producto'], c['estado']])
        row = cursor.fetchone()
        print("Raw Row:", row)
        print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
