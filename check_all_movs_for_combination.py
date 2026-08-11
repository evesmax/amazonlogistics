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

    print("--- ALL MOVEMENTS FOR BODEGA 1, FAB 20, MARCA 20, PROD 56, ESTADO 4 ---")
    sql = """
        SELECT idmovimiento, idtipomovimiento, foliodoctoorigen, doctoorigen, cantidad, cantidadsecundaria, fecha
        FROM inventarios_movimientos
        WHERE idfabricante = 20 AND idmarca = 20 AND idbodega = 1 AND idproducto = 56 AND idestadoproducto = 4
        ORDER BY fecha ASC, idmovimiento ASC
    """
    cursor.execute(sql)
    rows = cursor.fetchall()
    total_qty = 0.0
    total_sec_qty = 0.0
    for r in rows:
        print(f"Mov: {r['idmovimiento']} | Tipo: {r['idtipomovimiento']} | Folio: {r['foliodoctoorigen']} | Docto: {r['doctoorigen']} | Cant: {r['cantidad']} | Sec: {r['cantidadsecundaria']} | Fecha: {r['fecha']}")
        total_qty += float(r['cantidad'])
        total_sec_qty += float(r['cantidadsecundaria'])
    
    print(f"Total qty calculated from movements: {total_qty}")
    print(f"Total secondary qty calculated from movements: {total_sec_qty}")
    
    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
