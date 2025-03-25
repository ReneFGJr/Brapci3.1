import asyncio
import numpy as np
import sounddevice as sd
from openai import AsyncOpenAI
from pydub import AudioSegment
from io import BytesIO

class LocalAudioPlayerAndSaver:
    def __init__(self, samplerate=24000, channels=1, save_path="output.mp3"):
        self.samplerate = samplerate
        self.channels = channels
        self.save_path = save_path
        self.raw_audio = bytearray()  # Armazena os chunks de áudio

    async def play_and_save(self, stream):
        async for chunk in stream.iter_bytes():
            # Armazena para salvar depois
            self.raw_audio.extend(chunk)

            # Converte e toca
            audio_data = np.frombuffer(chunk, dtype=np.int16)
            float_data = audio_data.astype(np.float32) / 32768.0
            sd.play(float_data, samplerate=self.samplerate)
            await asyncio.sleep(len(float_data) / self.samplerate)
        sd.stop()

        # Salva o arquivo como MP3
        audio_segment = AudioSegment(
            data=bytes(self.raw_audio),
            sample_width=2,  # 16 bits = 2 bytes
            frame_rate=self.samplerate,
            channels=self.channels
        )
        audio_segment.export(self.save_path, format="mp3")
        print(f"Áudio salvo em: {self.save_path}")
