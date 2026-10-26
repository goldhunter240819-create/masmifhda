import pandas as pd
import json
import re

excel_file = r'C:\Users\WinProX11\Downloads\pkg.xlsx'
try:
    xl = pd.ExcelFile(excel_file)
    kompetensi_list = []
    
    # Iterate through sheets 1 to 14
    for i in range(1, 15):
        sheet_name = str(i)
        if sheet_name in xl.sheet_names:
            df = xl.parse(sheet_name)
            
            # The title of the competency is usually in the first row, first or second column
            # For example: "Penilaian untuk Kompetensi 1: Mengenal karakteristik peserta didik"
            title = ""
            for col in df.columns:
                if "Kompetensi" in str(col):
                    title = str(col)
                    break
            if not title:
                title = f"Kompetensi {i}"
            
            # Clean up title
            title = re.sub(r'Penilaian untuk Kompetensi \d+:\s*', '', title).strip()
            
            indikator_list = []
            
            # Look for rows that have numbers 1, 2, 3... in the first or second column
            # and the indicator text in the next column.
            for index, row in df.iterrows():
                row_vals = row.values.tolist()
                # find the number
                no = None
                text = None
                for idx, val in enumerate(row_vals):
                    if pd.notna(val) and str(val).strip().isdigit() and int(val) > 0 and int(val) < 20:
                        no = int(val)
                        # The text is usually in the next cell
                        if idx + 1 < len(row_vals) and pd.notna(row_vals[idx+1]):
                            text = str(row_vals[idx+1]).strip()
                            # remove newlines
                            text = re.sub(r'\s+', ' ', text)
                            break
                if no and text:
                    indikator_list.append({
                        "no": no,
                        "teks": text
                    })
            
            kompetensi_list.append({
                "id": i,
                "nama": title,
                "indikator": indikator_list
            })
            
    with open(r'c:\xampp\htdocs\masmifhda\apk\pkg_instrumen.json', 'w', encoding='utf-8') as f:
        json.dump(kompetensi_list, f, ensure_ascii=False, indent=4)
        
    print("Berhasil mengekstrak instrumen ke pkg_instrumen.json")

except Exception as e:
    print("Error:", str(e))
