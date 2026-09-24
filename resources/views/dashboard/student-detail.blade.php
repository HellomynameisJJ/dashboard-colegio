@extends('layouts.app')

@section('title', 'Competencias - ' . ($student->name ?? 'Estudiante'))

@section('content')
<!-- ESTILOS EXCLUSIVOS PARA IMPRESIÓN FORMAL EN HOJA A4 -->
<style>
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }
        
        /* Ocultar elementos interactivos y gráficos al imprimir */
        .no-print, header, nav, button, .print\:hidden, #themeToggleBtn {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
    }

    @media screen {
        .print-only {
            display: none !important;
        }
    }
</style>

<div class="max-w-7xl mx-auto space-y-8 p-4 sm:p-6 lg:p-8 transition-colors duration-300">

    <!-- ========================================== -->
    <!-- 1. VISTA INTERACTIVA WEB (NO IMPRESIÓN)    -->
    <!-- ========================================== -->
    <div class="no-print space-y-8">
        <!-- Topbar: Botón Volver + Acciones + Toggle Tema + Usuario -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-3xl shadow-xl transition-colors duration-300">
            <a href="{{ $student->classroom_id ? route('dashboard.classroom', ['id' => $student->classroom_id, 'course_id' => request('course_id', 'all')]) : route('dashboard.classroom') }}" 
               class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-xs font-bold transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-xl p-1">
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-600 border border-slate-200 dark:border-slate-700 group-hover:border-indigo-500 flex items-center justify-center text-slate-600 dark:text-slate-300 group-hover:text-white transition-all">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </div>
                <span>Volver al Dashboard del Aula</span>
            </a>

            <div class="flex items-center justify-between sm:justify-end gap-3 flex-wrap">
                <!-- Botón de Impresión -->
                <button onclick="window.print()" type="button" aria-label="Imprimir Informe" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 active:scale-95 transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span>Imprimir Informe Formal</span>
                </button>

                <!-- Toggle de Tema -->
                <button id="themeToggleBtn" type="button" aria-label="Cambiar Tema" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-95 transition-all duration-200 cursor-pointer" title="Cambiar tema">
                    <i id="themeToggleIcon" class="fa-solid fa-moon text-indigo-500 dark:text-amber-400 text-sm"></i>
                    <span id="themeToggleText" class="hidden sm:inline">Modo Oscuro</span>
                </button>

                @auth
                    <div class="flex items-center gap-3 border-l border-slate-200 dark:border-slate-800 pl-3">
                        <div class="relative">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-xs shadow-md shadow-indigo-500/20 ring-2 ring-indigo-400/40">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-white dark:border-slate-900 rounded-full"></span>
                        </div>
                        <div class="hidden md:flex flex-col">
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-200 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 capitalize font-semibold">{{ auth()->user()->role ?? 'Docente' }}</span>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- CÁLCULO DE COLECCIONES GLOBALES -->
        @php
            $selectedCourseFilter = request('course_id', 'all');
            $allCompetencies = collect($results)->flatten();
            $allScores = $allCompetencies->pluck('score')->filter(fn($val) => !is_null($val));
            $avgScore = $allScores->isNotEmpty() ? min(20.0, $allScores->avg()) : 0;
            $porcentajeGeneral = round(($avgScore / 20) * 100, 1);
            
            $topCompetency = $allCompetencies->sortByDesc('score')->first();
            $lowCompetency = $allCompetencies->sortBy('score')->first();
            $masteredCount = $allCompetencies->filter(fn($item) => ($item->score ?? 0) >= 10.5)->count();

            $chartLabels = [];
            $chartScores = [];
            foreach($results as $cName => $comps) {
                foreach($comps as $item) {
                    $chartLabels[] = $item->competency->name ?? $cName;
                    $chartScores[] = min(20.0, $item->score ?? 0);
                }
            }
        @endphp

        <!-- Header Perfil del Estudiante + KPIs Globales -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Banner Principal del Alumno -->
            <div class="lg:col-span-2 relative overflow-hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-xl transition-colors duration-300">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500/10 dark:bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-violet-500/10 dark:bg-violet-600/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 border border-indigo-400/30 flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-indigo-600/20">
                            {{ strtoupper(substr($student->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-6 h-6 bg-indigo-600 border-2 border-white dark:border-slate-900 rounded-full flex items-center justify-center text-[10px] text-white shadow">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </span>
                    </div>

                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-semibold mb-2">
                            <i class="fa-solid fa-id-card"></i> Expediente {{ $selectedCourseFilter !== 'all' ? '— Curso Filtrado' : '— Visión General' }}
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $student->name }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-3 mt-2">
                            <span class="inline-flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-medium">
                                <i class="fa-solid fa-school text-indigo-500 dark:text-indigo-400"></i>
                                {{ $student->classroom->name ?? 'Sin Aula Asignada' }}
                            </span>
                            <span class="text-slate-300 dark:text-slate-700">•</span>
                            <span class="inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                <i class="fa-regular fa-envelope text-slate-400"></i>
                                {{ $student->email ?? 'Sin correo registrado' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="relative z-10 pt-6 mt-6 border-t border-slate-200 dark:border-slate-800 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Cursos Evaluados</span>
                        <span class="text-base font-bold text-slate-900 dark:text-white">{{ count($results) }} Asignatura(s)</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Competencias Aprobadas</span>
                        <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ $masteredCount }} / {{ max(1, $allCompetencies->count()) }} Comp.</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Estado de Entrega</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-500/20 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span> Al día
                        </span>
                    </div>
                </div>
            </div>

            <!-- KPI Card Promedio General -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 flex flex-col justify-between shadow-xl transition-colors duration-300">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Promedio Consolidado</span>
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-600/10 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-lg">
                            <i class="fa-solid fa-award"></i>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ number_format($avgScore, 1) }}
                        </h3>
                        <span class="text-xs font-bold text-slate-400">/ 20.0</span>
                    </div>

                    <div class="mt-3">
                        @if($avgScore >= 18.0)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-xl border border-emerald-200 dark:border-emerald-500/20">
                                <i class="fa-solid fa-star"></i> Nivel AD - Logro Destacado
                            </span>
                        @elseif($avgScore >= 14.0)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-3 py-1 rounded-xl border border-blue-200 dark:border-blue-500/20">
                                <i class="fa-solid fa-circle-check"></i> Nivel A - Logro Esperado
                            </span>
                        @elseif($avgScore >= 11.0)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-3 py-1 rounded-xl border border-amber-200 dark:border-amber-500/20">
                                <i class="fa-solid fa-triangle-exclamation"></i> Nivel B - En Proceso
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 px-3 py-1 rounded-xl border border-rose-200 dark:border-rose-500/20">
                                <i class="fa-solid fa-circle-xmark"></i> Nivel C - En Inicio
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                    @if($topCompetency)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400 truncate pr-2"><i class="fa-solid fa-arrow-up-right-dots text-emerald-500 mr-1"></i> Fortaleza:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[140px]" title="{{ $topCompetency->competency->name ?? '' }}">{{ $topCompetency->competency->name ?? 'N/A' }}</span>
                        </div>
                    @endif
                    @if($lowCompetency)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400 truncate pr-2"><i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1"></i> A Reforzar:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[140px]" title="{{ $lowCompetency->competency->name ?? '' }}">{{ $lowCompetency->competency->name ?? 'N/A' }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Gráfico Radar -->
        @if(count($results) > 0)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl transition-colors duration-300">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg flex items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-indigo-600 dark:text-indigo-400"></i> Balance de Competencias del Curso
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Distribución del rendimiento académico en escala de 0 a 20</p>
                    </div>
                </div>

                <div class="relative h-72 sm:h-80 w-full flex items-center justify-center">
                    <canvas id="studentRadarChart"></canvas>
                </div>
            </div>
        @endif

        <!-- Desglose por Cursos y Competencias -->
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="font-bold text-slate-900 dark:text-white text-lg flex items-center gap-2">
                    <i class="fa-solid fa-book-bookmark text-indigo-600 dark:text-indigo-400"></i> Desglose Detallado por Asignatura
                </h3>

                @if(count($results) > 0)
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="courseSearchInput" placeholder="Buscar asignatura o competencia..." 
                               class="w-full pl-9 pr-4 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                @endif
            </div>

            <div id="coursesContainer" class="space-y-6">
                @forelse($results as $courseName => $competencies)
                    <div class="course-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl transition-colors duration-300 hover:border-slate-300 dark:hover:border-slate-700" data-course="{{ strtolower($courseName) }}">
                        <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                            <h4 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-600/10 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs">
                                    <i class="fa-solid fa-book"></i>
                                </span>
                                {{ $courseName }}
                            </h4>
                            <span class="text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-1 rounded-xl border border-slate-200 dark:border-slate-700/50">
                                {{ count($competencies) }} Competencias Evaluadas
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            @foreach($competencies as $res)
                                @php 
                                    $score = min(20.0, $res->score ?? 0); 
                                    $percentWidth = min(100, max(0, ($score / 20) * 100));
                                @endphp
                                <div class="competency-item bg-slate-50 dark:bg-slate-800/40 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800/60 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-indigo-200 dark:hover:border-slate-700/60 transition" data-competency="{{ strtolower($res->competency->name ?? '') }}">
                                    <div class="flex-1">
                                        <h5 class="font-bold text-slate-800 dark:text-slate-200 text-sm mb-1">
                                            {{ $res->competency->name ?? 'Competencia sin nombre' }}
                                        </h5>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 font-medium">
                                            <i class="fa-solid fa-circle-check text-slate-400 dark:text-slate-600"></i> Evaluación Sincronizada
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-6 justify-between md:justify-end">
                                        <div class="text-left md:text-right">
                                            <span class="block font-black text-slate-900 dark:text-white text-lg leading-tight">{{ number_format($score, 1) }} <span class="text-xs text-slate-400 font-bold">/ 20</span></span>
                                            <span class="text-xs font-extrabold">
                                                @if($score >= 18.0)
                                                    <span class="text-emerald-600 dark:text-emerald-400">AD • Logro Destacado</span>
                                                @elseif($score >= 14.0)
                                                    <span class="text-blue-600 dark:text-blue-400">A • Logro Esperado</span>
                                                @elseif($score >= 11.0)
                                                    <span class="text-amber-600 dark:text-amber-400">B • En Proceso</span>
                                                @else
                                                    <span class="text-rose-600 dark:text-rose-400">C • En Inicio</span>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="w-28 sm:w-36 bg-slate-200 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-300/60 dark:border-slate-700/40">
                                            <div class="h-full rounded-full transition-all duration-700 ease-out
                                                @if($score >= 18.0) bg-gradient-to-r from-emerald-500 to-teal-400
                                                @elseif($score >= 14.0) bg-gradient-to-r from-blue-500 to-indigo-400
                                                @elseif($score >= 11.0) bg-gradient-to-r from-amber-500 to-yellow-400
                                                @else bg-gradient-to-r from-rose-500 to-red-400 @endif" 
                                                style="width: {{ $percentWidth }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-12 text-center text-slate-500 dark:text-slate-400 shadow-xl transition-colors duration-300">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-400 dark:text-slate-500 text-2xl shadow-inner">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-base">Sin Evaluaciones</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Aún no existen calificaciones o competencias registradas para este filtro o estudiante.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- 2. DOCUMENTO FORMAL IMPRESO A4 (SE ACTIVA ÚNICAMENTE AL PULSAR IMPRIMIR) -->
    <!-- ========================================================================= -->
    <div class="print-only">
        
        <!-- ENCABEZADO OFICIAL -->
        <div style="border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1 style="font-size: 18px; font-weight: 900; margin: 0; color: #0f172a; text-transform: uppercase;">
                    I.E. TUNGASUCA — COMAS
                </h1>
                <p style="font-size: 11px; color: #334155; margin: 2px 0 0 0; font-weight: bold;">
                    INFORME PEDAGÓGICO DE EVALUACIÓN POR COMPETENCIAS (CNEB)
                </p>
                <p style="font-size: 9px; color: #64748b; margin: 2px 0 0 0;">
                    UGEL 04 — LIMA METROPOLITANA
                </p>
            </div>
            <div style="text-align: right;">
                <span style="font-size: 10px; font-weight: 800; border: 1.5px solid #0f172a; padding: 3px 8px; border-radius: 4px; display: inline-block;">
                    EXPEDIENTE ACADÉMICO
                </span>
                <p style="font-size: 9px; color: #64748b; margin: 4px 0 0 0;">
                    Emisión: {{ date('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <!-- CUADRO DE DATOS DEL ALUMNO -->
        <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <tr>
                    <td style="padding: 3px 0; width: 15%; font-weight: bold; color: #475569;">Estudiante:</td>
                    <td style="padding: 3px 0; width: 45%; font-weight: 800; font-size: 12px; color: #0f172a;">{{ strtoupper($student->name ?? 'N/A') }}</td>
                    <td style="padding: 3px 0; width: 15%; font-weight: bold; color: #475569;">Código:</td>
                    <td style="padding: 3px 0; width: 25%; font-weight: bold; color: #0f172a;">{{ $student->code ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 3px 0; font-weight: bold; color: #475569;">Aula / Grado:</td>
                    <td style="padding: 3px 0; font-weight: bold; color: #0f172a;">{{ $student->classroom->name ?? 'N/A' }}</td>
                    <td style="padding: 3px 0; font-weight: bold; color: #475569;">Condición:</td>
                    <td style="padding: 3px 0; font-weight: bold; color: #15803d;">MATRICULADO / REGULAR</td>
                </tr>
            </table>
        </div>

        <!-- TABLA FORMAL CON BORDES Y DESGLOSE COMPLETO -->
        <h2 style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #0f172a; margin-bottom: 8px; border-left: 3px solid #4f46e5; padding-left: 6px;">
            Consolidado Académico de Competencias Evaluadas
        </h2>

        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 16px;">
            <thead>
                <tr style="background-color: #0f172a; color: #ffffff; text-align: left;">
                    <th style="padding: 6px 8px; border: 1px solid #0f172a; width: 22%;">ASIGNATURA</th>
                    <th style="padding: 6px 8px; border: 1px solid #0f172a; width: 48%;">COMPETENCIA CURRICULAR</th>
                    <th style="padding: 6px 8px; border: 1px solid #0f172a; width: 10%; text-align: center;">NOTA</th>
                    <th style="padding: 6px 8px; border: 1px solid #0f172a; width: 10%; text-align: center;">NIVEL</th>
                    <th style="padding: 6px 8px; border: 1px solid #0f172a; width: 10%; text-align: center;">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @php $rowIndex = 0; @endphp
                @forelse($results as $courseName => $competencies)
                    @foreach($competencies as $res)
                        @php
                            $rowIndex++;
                            $score = min(20.0, $res->score ?? 0);
                            $isApproved = $score >= 10.5;
                            
                            if ($score >= 18.0) { $nivel = 'AD'; }
                            elseif ($score >= 14.0) { $nivel = 'A'; }
                            elseif ($score >= 11.0) { $nivel = 'B'; }
                            else { $nivel = 'C'; }

                            $bgColor = ($rowIndex % 2 == 0) ? '#f8fafc' : '#ffffff';
                        @endphp
                        <tr style="background-color: {{ $bgColor }};">
                            <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: bold; color: #1e293b;">
                                {{ $courseName }}
                            </td>
                            <td style="padding: 5px 8px; border: 1px solid #cbd5e1; color: #334155;">
                                {{ $res->competency->name ?? 'Competencia' }}
                            </td>
                            <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: 900; text-align: center; font-size: 10.5px; color: #0f172a;">
                                {{ number_format($score, 1) }}
                            </td>
                            <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: bold; text-align: center; color: #0f172a;">
                                {{ $nivel }}
                            </td>
                            <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: bold; text-align: center; color: {{ $isApproved ? '#15803d' : '#b91c1c' }};">
                                {{ $isApproved ? 'APROBADO' : 'EN INICIO' }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" style="padding: 10px; border: 1px solid #cbd5e1; text-align: center; color: #64748b;">
                            No se registran datos de evaluación disponibles.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- RESUMEN FINAL Y FIRMAS EN IMPRESIÓN -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 14px;">
            <!-- Leyenda CNEB -->
            <div style="width: 55%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px; font-size: 8.5px; color: #475569; background-color: #f8fafc;">
                <strong>ESCALA PEDAGÓGICA CNEB:</strong>
                <ul style="margin: 3px 0 0 12px; padding: 0;">
                    <li><strong>AD (18-20):</strong> Logro destacado sobre lo esperado.</li>
                    <li><strong>A (14-17):</strong> Logro satisfactorio de la competencia.</li>
                    <li><strong>B (11-13):</strong> En proceso de alcanzar el logro.</li>
                    <li><strong>C (00-10):</strong> En inicio, requiere apoyo docente.</li>
                </ul>
            </div>

            <!-- Promedio Consolidado -->
            <div style="width: 40%; border: 1.5px solid #0f172a; border-radius: 4px; padding: 8px; text-align: center; background-color: #ffffff;">
                <span style="font-size: 9px; font-weight: bold; color: #475569; text-transform: uppercase; display: block;">PROMEDIO CONSOLIDADO</span>
                <span style="font-size: 22px; font-weight: 900; color: #0f172a; margin-top: 1px; display: block;">
                    {{ number_format($avgScore, 1) }} / 20.0
                </span>
                <span style="font-size: 9px; font-weight: bold; color: #4f46e5;">
                    EQUIVALENCIA RELATIVA: {{ $porcentajeGeneral }}%
                </span>
            </div>
        </div>

        <!-- SECCIÓN DE FIRMAS -->
        <div style="margin-top: 55px; display: flex; justify-content: space-around; text-align: center; font-size: 9px; color: #334155;">
            <div style="width: 40%; border-top: 1px solid #0f172a; padding-top: 4px;">
                <strong>FIRMA DEL DOCENTE / TUTOR</strong><br>
                <span>I.E. Tungasuca - Comas</span>
            </div>
            <div style="width: 40%; border-top: 1px solid #0f172a; padding-top: 4px;">
                <strong>DIRECCIÓN ACADÉMICA / SELLO</strong><br>
                <span>I.E. Tungasuca - Comas</span>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeToggleIcon');
        const themeText = document.getElementById('themeToggleText');
        const html = document.documentElement;

        function applyTheme(isDark) {
            if (isDark) {
                html.classList.add('dark');
                if (themeIcon) themeIcon.className = 'fa-solid fa-sun text-amber-400 text-sm';
                if (themeText) themeText.textContent = 'Modo Claro';
                localStorage.setItem('theme', 'dark');
            } else {
                html.classList.remove('dark');
                if (themeIcon) themeIcon.className = 'fa-solid fa-moon text-indigo-500 text-sm';
                if (themeText) themeText.textContent = 'Modo Oscuro';
                localStorage.setItem('theme', 'light');
            }
        }

        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const initialIsDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
        
        applyTheme(initialIsDark);

        let radarChartInstance = null;
        const ctx = document.getElementById('studentRadarChart');

        if (ctx) {
            const rawLabels = {!! json_encode($chartLabels) !!};
            const dataScores = {!! json_encode($chartScores) !!};

            const labels = rawLabels.map(lbl => lbl.length > 25 ? lbl.substring(0, 22) + '...' : lbl);

            function getChartColors(isDark) {
                return {
                    bg: isDark ? 'rgba(99, 102, 241, 0.35)' : 'rgba(79, 70, 229, 0.2)',
                    border: isDark ? '#818cf8' : '#4f46e5',
                    pointBg: isDark ? '#a5b4fc' : '#4f46e5',
                    lines: isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(226, 232, 240, 0.8)',
                    labels: isDark ? '#cbd5e1' : '#475569',
                    tooltipBg: isDark ? '#0f172a' : '#ffffff',
                    tooltipText: isDark ? '#f8fafc' : '#0f172a'
                };
            }

            const colors = getChartColors(html.classList.contains('dark'));

            radarChartInstance = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels.length > 0 ? labels : ['Sin Datos'],
                    datasets: [{
                        label: 'Puntaje (0 - 20)',
                        data: dataScores.length > 0 ? dataScores : [0],
                        backgroundColor: colors.bg,
                        borderColor: colors.border,
                        borderWidth: 2,
                        pointBackgroundColor: colors.pointBg,
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: colors.border
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: colors.tooltipBg,
                            titleColor: colors.tooltipText,
                            bodyColor: colors.border,
                            borderColor: colors.lines,
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    const fullLabel = rawLabels[context.dataIndex] || context.label;
                                    return fullLabel + ': ' + context.raw + ' / 20';
                                }
                            }
                        }
                    },
                    scales: {
                        r: {
                            angleLines: { color: colors.lines },
                            grid: { color: colors.lines },
                            pointLabels: {
                                color: colors.labels,
                                font: { size: 10, weight: '600' }
                            },
                            ticks: {
                                color: colors.labels,
                                backdropColor: 'transparent',
                                stepSize: 4
                            },
                            min: 0,
                            max: 20
                        }
                    }
                }
            });

            window.updateRadarColors = function(isDark) {
                const c = getChartColors(isDark);
                radarChartInstance.data.datasets[0].backgroundColor = c.bg;
                radarChartInstance.data.datasets[0].borderColor = c.border;
                radarChartInstance.data.datasets[0].pointBackgroundColor = c.pointBg;
                radarChartInstance.options.scales.r.angleLines.color = c.lines;
                radarChartInstance.options.scales.r.grid.color = c.lines;
                radarChartInstance.options.scales.r.pointLabels.color = c.labels;
                radarChartInstance.options.scales.r.ticks.color = c.labels;
                radarChartInstance.options.plugins.tooltip.backgroundColor = c.tooltipBg;
                radarChartInstance.options.plugins.tooltip.titleColor = c.tooltipText;
                radarChartInstance.options.plugins.tooltip.bodyColor = c.border;
                radarChartInstance.options.plugins.tooltip.borderColor = c.lines;
                radarChartInstance.update();
            };
        }

        if (themeBtn) {
            themeBtn.addEventListener('click', function() {
                const isDarkNow = html.classList.contains('dark');
                applyTheme(!isDarkNow);
                if (radarChartInstance && typeof window.updateRadarColors === 'function') {
                    window.updateRadarColors(!isDarkNow);
                }
            });
        }

        // Filtro/Buscador Dinámico
        const searchInput = document.getElementById('courseSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                const courseCards = document.querySelectorAll('.course-card');

                courseCards.forEach(card => {
                    const courseName = card.getAttribute('data-course');
                    const compItems = card.querySelectorAll('.competency-item');
                    let matchInCompetency = false;

                    compItems.forEach(item => {
                        const compName = item.getAttribute('data-competency');
                        if (compName.includes(query)) {
                            item.style.display = '';
                            matchInCompetency = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (courseName.includes(query) || matchInCompetency) {
                        card.style.display = '';
                        if (courseName.includes(query)) {
                            compItems.forEach(i => i.style.display = '');
                        }
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection