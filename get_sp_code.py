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

    print("--- SHOW CREATE PROCEDURE cancelacion_recepciones ---")
    try:
        cursor.execute("SHOW CREATE PROCEDURE cancelacion_recepciones")
        row = cursor.fetchone()
        if row:
            print(row['Create Procedure'])
        else:
            print("Procedure cancelacion_recepciones not found.")
    except Exception as e:
        print("Error showing procedure cancelacion_recepciones:", e)
    print()

    print("--- SHOW CREATE PROCEDURE cancelacion_envios ---")
    try:
        cursor.execute("SHOW CREATE PROCEDURE cancelacion_envios")
        row = cursor.fetchone()
        if row:
            print(row['Create Procedure'])
        else:
            print("Procedure cancelacion_envios not found.")
    except Exception as e:
        print("Error showing procedure cancelacion_envios:", e)
    print()

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
