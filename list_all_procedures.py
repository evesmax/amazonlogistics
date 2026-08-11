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

    print("--- STORED PROCEDURES IN DATABASE ---")
    cursor.execute("SHOW PROCEDURE STATUS WHERE Db = '_dbmlog0000018677'")
    procs = cursor.fetchall()
    print(f"Found {len(procs)} procedures:")
    for p in procs:
        name = p['Name']
        print(f"- Name: {name}")
        
        # Get its definition
        cursor.execute(f"SHOW CREATE PROCEDURE {name}")
        p_def = cursor.fetchone()
        if p_def:
            code = p_def['Create Procedure']
            if "delete from inventarios_movimientos" in code.lower():
                print(f"  [WARNING] Procedure {name} contains deletion from inventarios_movimientos!")
                # Print code lines containing DELETE
                for line in code.split("\n"):
                    if "delete" in line.lower() or "inventarios_movimientos" in line.lower():
                        print(f"    Code: {line.strip()}")
        print("-" * 50)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
