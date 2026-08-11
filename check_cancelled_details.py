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

    target_ids = (5767, 5776, 5794, 5795, 5823)
    sql = f"SELECT idrecepcion, idtraslado, idenvio, cantidadrecibida1, cantidadrecibida2, cantidadenviada1, cantidadenviada2, diferencia1, diferencia2, idestadodocumento FROM logistica_recepciones WHERE idrecepcion IN {target_ids}"
    cursor.execute(sql)
    for r in cursor.fetchall():
        print(r)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
