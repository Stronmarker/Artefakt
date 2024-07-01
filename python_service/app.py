from flask import Flask, request, jsonify, url_for
import os
import fitz  # PyMuPDF
import logging

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = 'uploads'
app.config['OUTPUT_FOLDER'] = 'output'

# Assurez-vous que les dossiers existent
os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)
os.makedirs(app.config['OUTPUT_FOLDER'], exist_ok=True)

# Configurer les journaux
logging.basicConfig(level=logging.INFO)

@app.route('/api/crop_convert', methods=['POST'])
def crop_convert_pdf_to_png_api():
    logging.info('Received a POST request for cropping and converting PDF to PNG')
    
    if 'file' not in request.files:
        logging.error('No file part in the request')
        return jsonify({'error': 'Aucun fichier rentré'}), 400
    
    file = request.files['file']
    if file.filename == '':
        logging.error('No selected file')
        return jsonify({'error': 'Aucun fichier sélectionné'}), 400
    
    if not file.filename.endswith('.pdf'):
        logging.error('Uploaded file is not a PDF')
        return jsonify({'error': 'Importez un PDF'}), 400
    
    dimensions = request.form.get('dimensions')
    if dimensions not in ["84x55", "148x148", "148x105"]:
        logging.error('Invalid dimensions')
        return jsonify({'error': 'Dimensions non valides'}), 400
    
    file_path = os.path.join(app.config['UPLOAD_FOLDER'], file.filename)
    file.save(file_path)
    logging.info(f'File saved to {file_path}')
    
    origin_name = os.path.splitext(file.filename)[0]
    output_folder = os.path.join(app.config['OUTPUT_FOLDER'], origin_name)
    os.makedirs(output_folder, exist_ok=True)
    
    try:
        png_files = crop_and_convert_pdf_to_png(file_path, output_folder, dimensions)
        logging.info(f'Cropped and converted PNGs saved to {output_folder}')

        # Generate URLs for the PNG files
        png_urls = [url_for('static', filename=os.path.join('output', origin_name, os.path.basename(png)), _external=True) for png in png_files]

        return jsonify({'png_urls': png_urls})
    except Exception as e:
        logging.error(f'Error cropping and converting PDF: {str(e)}')
        return jsonify({'error': str(e)}), 500

def crop_and_convert_pdf_to_png(input_pdf, output_folder, dimensions):
    pdf_document = fitz.open(input_pdf)
    conversion = 72 / 25.4

    if dimensions == "148x148":
        crop_width = 148 * conversion
        crop_height = 148 * conversion
    elif dimensions == "84x55":
        crop_width = 84 * conversion
        crop_height = 55 * conversion
    elif dimensions == "148x105":
        crop_width = 105 * conversion
        crop_height = 148 * conversion
    else:
        raise ValueError("Dimensions non valides")

    png_files = []

    for page_num in range(len(pdf_document)):
        page = pdf_document.load_page(page_num)
        rect = page.rect

        center_x = rect.width / 2
        center_y = rect.height / 2

        crop_rect = fitz.Rect(
            center_x - crop_width / 2,
            center_y - crop_height / 2,
            center_x + crop_width / 2,
            center_y + crop_height / 2
        )

        page.set_cropbox(crop_rect)

        # Convertir chaque page en une image avec une résolution plus élevée
        zoom = 4  # Facteur de zoom pour augmenter la résolution
        mat = fitz.Matrix(zoom, zoom)
        pix = page.get_pixmap(matrix=mat, alpha=False)
        output_png = os.path.join(output_folder, f'page_{page_num + 1}.png')
        pix.save(output_png)
        png_files.append(output_png)
    
    pdf_document.close()
    return png_files

if __name__ == '__main__':
    app.run(debug=True)
