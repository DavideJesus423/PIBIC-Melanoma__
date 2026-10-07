@extends('layout.main')
@section('title', 'Resultado da Análise')

@section('content')
<div class="page-content">
    <main style="padding: 2rem 0 4rem;">
        <div class="container" style="max-width: 680px; margin: 0 auto;">

            {{-- Imagem --}}
            @if(isset($result['image_path']))
            <div class="animate-fadeIn text-center mb-5">
                <img src="{{ $result['image_path'] }}" alt="Imagem analisada" style="
                    width: 220px; height: 220px;
                    object-fit: cover;
                    border-radius: var(--radius-xl);
                    box-shadow: var(--shadow-xl);
                    border: 4px solid {{ ($result['melanoma'] ?? false) ? 'var(--danger)' : 'var(--success)' }};
                ">
            </div>
            @endif

            {{-- Card principal do resultado --}}
            <div class="animate-fadeIn delay-100" style="
                background: {{ ($result['melanoma'] ?? false)
                    ? 'linear-gradient(135deg, #fef2f2 0%, #fff 100%)'
                    : 'linear-gradient(135deg, #f0fdf4 0%, #fff 100%)' }};
                border: 2px solid {{ ($result['melanoma'] ?? false) ? 'var(--danger)' : 'var(--success)' }};
                border-radius: var(--radius-xl);
                padding: 2.5rem 2rem;
                text-align: center;
                margin-bottom: 1.5rem;
                box-shadow: var(--shadow-lg);
            ">
                {{-- Ícone --}}
                <div style="
                    width: 80px; height: 80px;
                    border-radius: 50%;
                    background: {{ ($result['melanoma'] ?? false) ? 'var(--danger)' : 'var(--success)' }};
                    display: flex; align-items: center; justify-content: center;
                    margin: 0 auto 1.25rem;
                    box-shadow: 0 8px 24px {{ ($result['melanoma'] ?? false) ? 'rgba(239,68,68,0.3)' : 'rgba(16,185,129,0.3)' }};
                ">
                    <i class="bi {{ ($result['melanoma'] ?? false) ? 'bi-exclamation-triangle-fill' : 'bi-check-lg' }}"
                       style="color: white; font-size: 2rem;"></i>
                </div>

                {{-- Título --}}
                <h2 style="
                    font-size: 1.75rem;
                    color: {{ ($result['melanoma'] ?? false) ? 'var(--danger)' : 'var(--success)' }};
                    margin-bottom: 0.5rem;
                ">{{ $result['resultado'] ?? '—' }}</h2>

                {{-- Badge risco --}}
                <span style="
                    display: inline-block;
                    background: {{ ($result['melanoma'] ?? false) ? 'var(--danger)' : 'var(--success)' }};
                    color: white;
                    padding: 0.35rem 1.25rem;
                    border-radius: var(--radius-full);
                    font-size: 0.85rem;
                    font-weight: 600;
                    margin-bottom: 2rem;
                ">Risco {{ $result['nivel_risco'] ?? '—' }}</span>

                {{-- Confiança --}}
                @if(isset($result['confianca']))
                <div style="margin-bottom: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.875rem; color: var(--gray-500); font-weight: 500;">Confiança da IA</span>
                        <span style="font-size: 0.875rem; font-weight: 700; color: {{ ($result['melanoma'] ?? false) ? 'var(--danger)' : 'var(--success)' }};">
                            {{ $result['confianca'] }}%
                        </span>
                    </div>
                    <div style="background: var(--gray-200); border-radius: var(--radius-full); height: 8px; overflow: hidden;">
                        <div style="
                            width: {{ $result['confianca'] }}%;
                            height: 8px;
                            border-radius: var(--radius-full);
                            background: {{ ($result['melanoma'] ?? false) ? 'var(--danger)' : 'var(--success)' }};
                            transition: width 1s ease;
                        "></div>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--gray-400); margin-top: 0.4rem;">
                        @if($result['confianca'] < 50)
                            Confiança baixa — resultado inconclusivo, consulte um especialista
                        @elseif($result['confianca'] < 75)
                            Confiança moderada — recomendamos avaliação profissional
                        @else
                            Confiança alta na predição do modelo
                        @endif
                    </p>
                </div>
                @endif
            </div>

            {{-- Recomendação --}}
            @if(isset($result['recomendacao']))
            <div class="card animate-fadeIn delay-200 mb-4" style="border-left: 4px solid var(--primary);">
                <div class="card-body" style="display: flex; gap: 1rem; align-items: center;">
                    <div style="
                        width: 48px; height: 48px; flex-shrink: 0;
                        background: var(--primary-light);
                        border-radius: var(--radius-lg);
                        display: flex; align-items: center; justify-content: center;
                    ">
                        <i class="bi bi-clipboard2-pulse" style="color: var(--primary); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 0.95rem; margin-bottom: 0.25rem; color: var(--gray-800);">Recomendação</h4>
                        <p style="color: var(--gray-500); margin: 0; font-size: 0.9rem;">{{ $result['recomendacao'] }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Aviso --}}
            @if(isset($result['aviso']))
            <div class="animate-fadeIn delay-300 mb-5" style="
                background: #fffbeb;
                border: 1px solid #fcd34d;
                border-radius: var(--radius-xl);
                padding: 1.25rem 1.5rem;
                display: flex; gap: 1rem; align-items: start;
            ">
                <i class="bi bi-info-circle-fill" style="color: var(--warning); font-size: 1.25rem; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <h4 style="font-size: 0.875rem; font-weight: 700; color: #92400e; margin-bottom: 0.25rem;">Aviso importante</h4>
                    <p style="font-size: 0.8rem; color: #92400e; margin: 0; line-height: 1.6;">{{ $result['aviso'] }}</p>
                </div>
            </div>
            @endif

            {{-- Botões --}}
            <div class="animate-fadeIn delay-300 text-center" style="display: flex; gap: 1rem; justify-content: center;">
                <a href="{{ route('upload') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-repeat"></i>
                    Nova Análise
                </a>
                <a href="{{ route('index') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-house"></i>
                    Início
                </a>
            </div>

        </div>
    </main>
</div>
@endsection