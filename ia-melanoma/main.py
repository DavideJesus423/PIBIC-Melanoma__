import io
import numpy as np
from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from PIL import Image
import tensorflow as tf

app = FastAPI(
    title="Melanoma AI API",
    description="Microsserviço de análise de imagens para detecção de melanoma",
    version="2.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["POST", "GET"],
    allow_headers=["*"],
)

MODEL_PATH = "Models/Modelo_Treinado_Mel_API.keras"
IMG_SIZE   = (128, 128)

AVISO_LIMITACAO = (
    "Este resultado é gerado por uma IA com aproximadamente 85% de acurácia geral. "
    "Não substitui a avaliação de um dermatologista. "
    "Use como ferramenta de triagem apenas."
)

model = None
try:
    model = tf.keras.models.load_model(MODEL_PATH)
    print(f"✅ Modelo carregado com sucesso: {MODEL_PATH}")
    print(f"   Input shape:  {model.input_shape}")
    print(f"   Output shape: {model.output_shape}")
except Exception as e:
    print(f"❌ Erro ao carregar modelo: {e}")
    model = None


def preprocess_image(image_bytes: bytes) -> np.ndarray:
    img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    img = img.resize(IMG_SIZE)
    return np.expand_dims(np.array(img, dtype=np.float32), axis=0)


@app.get("/health")
def health():
    return {
        "status": "ok",
        "modelo_carregado": model is not None,
        "modelo": MODEL_PATH if model is not None else None,
    }


@app.post("/analisar")
async def analisar(imagem: UploadFile = File(...)):
    if model is None:
        raise HTTPException(status_code=503, detail="Modelo de IA não está disponível.")

    if imagem.content_type not in {"image/jpeg", "image/png", "image/jpg"}:
        raise HTTPException(status_code=422, detail="Use JPEG ou PNG.")

    try:
        predictions = model.predict(preprocess_image(await imagem.read()))[0]
        idx         = int(np.argmax(predictions))
        probability = float(predictions[idx])
        n_classes   = len(predictions)

        if n_classes == 2:
            # Modelo binário: 0 = não melanoma, 1 = melanoma
            is_melanoma = idx == 1
        elif n_classes == 8:
            # Modelo ISIC 2019
            CLASS_NAMES = ["AK", "BCC", "BKL", "DF", "MEL", "NV", "SCC", "VASC"]
            is_melanoma = CLASS_NAMES[idx] == "MEL"
        else:
            # Fallback: última classe = melanoma
            is_melanoma = idx == (n_classes - 1)

        return {
            "resultado":   "Melanoma detectado" if is_melanoma else "Não aparenta ser melanoma",
            "melanoma":    is_melanoma,
            "confianca":   round(probability * 100, 1),  # ex: 84.8 (em %)
            "nivel_risco": "Alto" if is_melanoma else "Baixo",
            "recomendacao": (
                "Procure um dermatologista com urgência."
                if is_melanoma else
                "Mantenha acompanhamento dermatológico regular."
            ),
            "aviso": AVISO_LIMITACAO,
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao processar imagem: {str(e)}")
