import sys
import pandas as pd
import numpy as np
import json
import re
import datetime

def analizar_reembolsos(ruta_d01, ruta_freshdesk):
    try:
        # --- Cargar y Limpiar D01 ---
        df_d01 = pd.read_csv(ruta_d01, sep=',', dtype=str)
        df_d01.columns = df_d01.columns.str.strip()
        df_d01['Ppal $'] = pd.to_numeric(df_d01['Ppal $'].str.replace('.', '', regex=False).str.replace(',', '.', regex=False), errors='coerce').fillna(0)
        
        # --- CORRECCIÓN: Asegurar que MTCN es texto y está limpio ---
        df_d01['MTCN'] = df_d01['MTCN'].astype(str).str.strip()

        df_reembolsos_d01_raw = df_d01[(df_d01['Servicio'] == 'COMPRA DE DIVISAS PAGO INTERNACIONAL WU') & (df_d01['Pais'] == 'VENEZUELA')].copy()
        df_envios = df_d01[df_d01['Servicio'] == 'VENTA DE DIVISAS ENVIO INTERNACIONAL WU '].copy()
        
        # --- Cruce de datos D01 vs D01 ---
        reembolsos_d01 = pd.merge(
            df_reembolsos_d01_raw[['MTCN', 'Ppal $']],
            df_envios[['MTCN', 'Pais']],
            on='MTCN', how='inner'
        )
        
        # Lista para guardar todos los dataframes de reembolsos encontrados
        lista_reembolsos = [reembolsos_d01]

        # --- Procesamiento Opcional de Freshdesk ---
        if ruta_freshdesk != "None":
            df_freshdesk = pd.read_csv(ruta_freshdesk, sep=',', dtype=str, usecols=['Asunto'])
            df_freshdesk.dropna(subset=['Asunto'], inplace=True)

            def extraer_mtcn(asunto):
                if "[ ref:!" in asunto: return None
                match = re.search(r'\(MTCN\)\s*(\d+)', asunto)
                if match: return str(int(match.group(1)))
                return None
            
            df_freshdesk['MTCN'] = df_freshdesk['Asunto'].apply(extraer_mtcn)
            df_freshdesk.dropna(subset=['MTCN'], inplace=True)
            
            if not df_freshdesk.empty:
                reembolsos_freshdesk = pd.merge(
                    df_freshdesk[['MTCN']],
                    df_envios[['MTCN', 'Pais', 'Ppal $']],
                    on='MTCN', how='inner'
                )
                if not reembolsos_freshdesk.empty:
                    lista_reembolsos.append(reembolsos_freshdesk)

        # --- Consolidación y Agregación Final ---
        if not lista_reembolsos:
            return json.dumps({"tabla_final": []})
            
        df_todos_reembolsos = pd.concat(lista_reembolsos, ignore_index=True).drop_duplicates(subset=['MTCN'])

        if df_todos_reembolsos.empty:
            return json.dumps({"tabla_final": []})
        
        resumen_por_pais = df_todos_reembolsos.groupby('Pais').agg(
            Reembolsos=('MTCN', 'count'),
            Monto_Reembolsos=('Ppal $', 'sum')
        ).reset_index()

        orden_paises = ['MEXICO', 'COLOMBIA', 'GUATEMALA', 'VENEZUELA', 'UNITED STATES', 'HONDURAS', 'SPAIN', 'PANAMA', 'ECUADOR', 'COSTA RICA', 'PERU', 'CHILE', 'NIGERIA', 'CHINA', 'BRAZIL', 'NICARAGUA', 'DOMINICAN REPUBLIC', 'ARGENTINA', 'BENIN', 'FRANCE', 'TURKEY', 'ITALY', 'KENYA']
        tabla_final = []
        paises_procesados = set()

        for pais in orden_paises:
            data = resumen_por_pais[resumen_por_pais['Pais'].str.upper() == pais]
            if not data.empty:
                tabla_final.append(data.to_dict('records')[0])
                paises_procesados.add(pais)
        
        df_otros = resumen_por_pais[~resumen_por_pais['Pais'].str.upper().isin(paises_procesados)]
        if not df_otros.empty:
            otros_sum = df_otros.agg({'Reembolsos': 'sum', 'Monto_Reembolsos': 'sum'})
            tabla_final.append({'Pais': 'Paises con 1 o 2 reembolsos', 'Reembolsos': otros_sum['Reembolsos'], 'Monto_Reembolsos': otros_sum['Monto_Reembolsos']})

        total_general = resumen_por_pais.agg({'Reembolsos': 'sum', 'Monto_Reembolsos': 'sum'})
        tabla_final.append({'Pais': 'Total general', 'Reembolsos': total_general['Reembolsos'], 'Monto_Reembolsos': total_general['Monto_Reembolsos']})

        return json.dumps({"tabla_final": tabla_final})

    except Exception as e:
        return json.dumps({"error": str(e)})

if __name__ == "__main__":
    ruta_d01_arg = sys.argv[1]
    ruta_freshdesk_arg = sys.argv[2]
    resultado_json = analizar_reembolsos(ruta_d01_arg, ruta_freshdesk_arg)
    print(resultado_json)