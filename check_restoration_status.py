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

    target_receptions = (5763, 5772, 5790, 5791, 5819)
    print("--- CHECKING MISSING MOVEMENTS STATUS ---")
    sql = f"SELECT idmovimiento, foliodoctoorigen, doctoorigen, idtipomovimiento, cantidad, cantidadsecundaria FROM inventarios_movimientos WHERE foliodoctoorigen IN {target_receptions} AND doctoorigen = 4"
    cursor.execute(sql)
    rows = cursor.fetchall()
    if rows:
        print(f"Found {len(rows)} movements:")
        for r in rows:
            print(r)
    else:
        print("No movements found for the target receptions.")
    
    print("\n--- CHECKING STORED PROCEDURE cancelacion_recepciones CODE ---")
    cursor.execute("SHOW CREATE PROCEDURE cancelacion_recepciones")
    sp = cursor.fetchone()
    if sp:
        print("Stored Procedure SQL:")
        print(sp['Create Procedure'])
    else:
        print("Procedure cancelacion_recepciones not found!")

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
