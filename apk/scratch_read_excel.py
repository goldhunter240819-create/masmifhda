import pandas as pd
import sys

excel_file = r'C:\Users\WinProX11\Downloads\pkg.xlsx'
with open(r'c:\xampp\htdocs\masmifhda\apk\scratch_pkg_output_utf8.txt', 'w', encoding='utf-8') as f:
    try:
        xl = pd.ExcelFile(excel_file)
        f.write('Sheets: ' + str(xl.sheet_names) + '\n')
        for s in xl.sheet_names:
            f.write('\n' + '='*50 + '\n')
            f.write('Sheet: ' + s + '\n')
            f.write('='*50 + '\n')
            df = xl.parse(s)
            f.write(df.head(20).to_string() + '\n')
    except Exception as e:
        f.write("Error: " + str(e) + '\n')
