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

    print("--- UNIQUE DOCTOORIGEN AND TIPOMOVIMIENTOS ---")
    cursor.execute("SELECT DISTINCT doctoorigen, idtipomovimiento FROM inventarios_movimientos")
    for r in cursor.fetchall():
        print(r)
    print()

    print("--- DO WE HAVE ANY MOVEMENTS WHERE foliodoctoorigen IS A SHIPMENT ID (e.g. from logistica_envios)? ---")
    # Let's count movements where doctoorigen = 3 (envio)
    cursor.execute("SELECT count(*) as count FROM inventarios_movimientos WHERE doctoorigen = 3")
    print("Movements with doctoorigen = 3 (Shipment/Envio):", cursor.fetchone()['count'])

    cursor.execute("SELECT count(*) as count FROM inventarios_movimientos WHERE doctoorigen = 4")
    print("Movements with doctoorigen = 4 (Reception):", cursor.fetchone()['count'])

    cursor.close()
    conn.conn = None

if __name__ == "__main__":
    main()
