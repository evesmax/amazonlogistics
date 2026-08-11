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

    target_shipments = (5763, 5772, 5790, 5791, 5819)

    print("--- CHECKING TARGET SHIPMENTS IN logistica_envios ---")
    sql = f"SELECT * FROM logistica_envios WHERE idenvio IN {target_shipments}"
    cursor.execute(sql)
    for e in cursor.fetchall():
        print(f"ID Envio: {e['idenvio']} | Traslado: {e['idtraslado']} | Cons. Bodega: {e['consecutivobodega']} | Fecha: {e['fechaenvio']} | EstatusDoc: {e['idestadodocumento']}")
    print()

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
