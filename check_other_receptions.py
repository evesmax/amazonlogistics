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

    reception_ids = (5767, 5776, 5794, 5795, 5823)

    print("--- CHECKING STATUS OF RECEPTIONS ---")
    sql = f"SELECT idrecepcion, consecutivobodega, idenvio, fecharecepcion, idbodega, idestadodocumento FROM logistica_recepciones WHERE idrecepcion IN {reception_ids}"
    cursor.execute(sql)
    for r in cursor.fetchall():
        print(r)
    print()

    print("--- CHECKING FOR MOVEMENTS IN inventarios_movimientos ---")
    sql = f"SELECT * FROM inventarios_movimientos WHERE foliodoctoorigen IN {reception_ids} AND doctoorigen = 4"
    cursor.execute(sql)
    for m in cursor.fetchall():
        print(f"Mov: {m['idmovimiento']} | FolioOrigen: {m['foliodoctoorigen']} | Lote: {m['idloteproducto']} | Producto: {m['idproducto']} | Cant: {m['cantidad2']} | Fecha: {m['fecha']}")
    print()

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
