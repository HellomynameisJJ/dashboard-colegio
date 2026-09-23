@extends('layouts.app')

@section('title', 'Dashboard de Aula - ' . ($currentClassroom->name ?? 'Salón') . ' | I.E. Tungasuca')

@section('content')
<div class="flex flex-col lg:flex-row min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">

    <!-- SIDEBAR DE CURSOS (Panel Izquierdo) -->
    <aside class="w-full lg:w-72 bg-white/90 dark:bg-slate-900/90 border-r border-slate-200 dark:border-slate-800 p-4 sm:p-6 space-y-6 flex-shrink-0 backdrop-blur-2xl">
        <div class="flex items-center gap-3 px-2">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/30">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-sm tracking-wide uppercase">Cursos</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Áreas Curriculares</p>
            </div>
        </div>

        <nav class="space-y-2">
            <!-- APARTADO PRINCIPAL: TODOS LOS CURSOS -->
            @php 
                $selectedCourseId = request('course_id', 'all');
                $isAllActive = empty($selectedCourseId) || $selectedCourseId === 'all'; 
            @endphp
            <a href="{{ route('dashboard.classroom', ['id' => $currentClassroom->id ?? 1, 'course_id' => 'all']) }}" 
               class="flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition-all duration-200 border {{ $isAllActive ? 'bg-gradient-to-r from-indigo-600 to-violet-600 border-indigo-400/50 text-white shadow-lg shadow-indigo-500/30 scale-[1.02]' : 'bg-slate-100/60 dark:bg-slate-800/40 border-slate-200/60 dark:border-slate-700/50 text-slate-700 dark:text-slate-300 hover:bg-slate-200/80 dark:hover:bg-slate-700/60' }}">
                <div class="flex items-center gap-3 overflow-hidden">
                    <i class="fa-solid fa-border-all text-sm"></i>
                    <span class="truncate">Todos los Cursos</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i>
            </a>

            <div class="pt-2 pb-1 px-2 border-t border-slate-200 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Asignaturas Específicas</span>
            </div>

            <!-- CURSOS INDIVIDUALES -->
            @forelse($courses as $course)
                @php 
                    $isActive = isset($currentCourse) && $currentCourse->id === $course->id && !$isAllActive;
                @endphp
                <a href="{{ route('dashboard.classroom', ['id' => $currentClassroom->id ?? 1, 'course_id' => $course->id]) }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition-all duration-200 border {{ $isActive ? 'bg-gradient-to-r from-indigo-600 to-violet-600 border-indigo-400/50 text-white shadow-lg shadow-indigo-500/30 scale-[1.02]' : 'bg-slate-100/60 dark:bg-slate-800/40 border-slate-200/60 dark:border-slate-700/50 text-slate-700 dark:text-slate-300 hover:bg-slate-200/80 dark:hover:bg-slate-700/60' }}">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <i class="fa-solid {{ $isActive ? 'fa-folder-open' : 'fa-folder' }} text-sm"></i>
                        <span class="truncate">{{ $course->name }}</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i>
                </a>
            @empty
                <p class="text-xs text-slate-500 px-2 py-4">No hay cursos registrados.</p>
            @endforelse
        </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL DEL DASHBOARD -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-x-hidden">

        <!-- Topbar: Selector de Aulas + Toggle Tema + Perfil de Usuario -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-slate-200 dark:border-slate-700/60 p-4 rounded-3xl shadow-xl dark:shadow-2xl transition-colors duration-300">
            <!-- Selector de Aulas con Conteo Dinámico -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-2 hidden sm:inline-block">Aulas:</span>
                @foreach($classrooms as $classroom)
                    <a href="{{ route('dashboard.classroom', ['id' => $classroom->id, 'course_id' => request('course_id', 'all')]) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl font-semibold text-xs transition-all duration-300 border {{ isset($currentClassroom) && $currentClassroom->id === $classroom->id ? 'bg-gradient-to-r from-indigo-600 to-violet-600 border-indigo-400/50 text-white shadow-lg shadow-indigo-500/30 scale-105' : 'bg-slate-100 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700/60 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700/80' }}">
                        <i class="fa-solid fa-graduation-cap text-xs"></i> 
                        {{ $classroom->name }}
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold {{ isset($currentClassroom) && $currentClassroom->id === $classroom->id ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                            {{ $classroom->students_count ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="flex items-center justify-between lg:justify-end gap-4 pl-0 lg:pl-6 border-t lg:border-t-0 border-slate-200 dark:border-slate-800 pt-3 lg:pt-0">
                <!-- Toggle de Tema (Claro / Oscuro) -->
                <button id="themeToggleBtn" type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200" title="Cambiar tema">
                    <i id="themeToggleIcon" class="fa-solid fa-moon text-indigo-500 dark:text-amber-400 text-sm"></i>
                    <span id="themeToggleText" class="hidden sm:inline">Modo Oscuro</span>
                </button>

                <!-- Perfil del usuario -->
                @auth
                    <div class="flex items-center gap-3 border-l border-slate-200 dark:border-slate-800 pl-4">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-black text-sm shadow-md shadow-purple-500/20 ring-2 ring-indigo-400/40">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-white dark:border-slate-900 rounded-full shadow-sm animate-pulse"></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-extrabold text-slate-900 dark:text-white leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[11px] text-indigo-600 dark:text-indigo-300 font-semibold capitalize">{{ auth()->user()->role ?? 'Docente / Admin' }}</span>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        @if($currentClassroom)
            <!-- BANNER PRINCIPAL + KPIs RESUMEN (ESTRUCTURA CORREGIDA) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Banner del Salón Actual (Ocupa 2 columnas) -->
                <div class="lg:col-span-2 relative overflow-hidden bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60 rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-xl dark:shadow-2xl transition-colors duration-300">
                    <div class="absolute -top-20 -right-20 w-72 h-72 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-violet-500/10 dark:bg-violet-500/20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/20 border border-indigo-200 dark:border-indigo-400/30 text-indigo-600 dark:text-indigo-300 text-xs font-bold mb-3 shadow-inner">
                            <i class="fa-solid fa-school text-indigo-500 dark:text-indigo-400"></i> I.E. Tungasuca - Comas
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                            {{ $currentClassroom->name }} — {{ ($currentCourse && !$isAllActive) ? $currentCourse->name : 'Visión General' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-xl font-medium leading-relaxed">
                            {{ ($currentCourse && !$isAllActive) ? 'Análisis detallado sobre el rendimiento por competencias para el área de ' . $currentCourse->name : 'Análisis consolidado en tiempo real sobre el rendimiento por competencias de todas las asignaturas.' }}
                        </p>
                    </div>

                    <div class="relative z-10 pt-6 mt-6 border-t border-slate-200 dark:border-slate-800/80 flex flex-wrap gap-8 items-center justify-between">
                        <div class="flex flex-wrap gap-8 items-center">
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Estado de Aula</span>
                                <span class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-xl border border-emerald-200 dark:border-emerald-500/30 shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-ping"></span> Activo
                                </span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Total Alumnos</span>
                                <span class="text-xl font-black text-slate-900 dark:text-white">{{ $students->count() }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">Matriculados</span></span>
                            </div>
                        </div>

                        <a href="#tabla-alumnos" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white border border-indigo-200 dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold transition-all duration-300 shadow-sm group">
                            <span>Ver Estudiantes</span>
                            <i class="fa-solid fa-arrow-down text-xs group-hover:translate-y-0.5 transition-transform duration-200"></i>
                        </a>
                    </div>
                </div>

                <!-- COLUMNA DERECHA CON TARJETAS APILADAS (Ocupa 1 columna) -->
                <div class="flex flex-col gap-4">
                    <!-- Promedio General en Porcentaje -->
                    <div class="flex-1 group bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60 hover:border-indigo-500/50 rounded-3xl p-5 backdrop-blur-2xl flex items-center justify-between shadow-xl transition-all duration-300 hover:translate-y-[-2px]">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Promedio {{ ($currentCourse && !$isAllActive) ? 'Curso' : 'General' }}</p>
                            @php
                                $notaVigesimal = $courseStats['promedio'] ?? $promedioGeneral;
                                // Conversión a escala porcentaje 0-100%
                                $porcentajePromedio = round(($notaVigesimal / 20) * 100, 1);
                            @endphp
                            <div class="flex items-center gap-2 mt-1">
                                <h4 class="text-3xl font-black text-slate-900 dark:text-white">{{ $porcentajePromedio }}%</h4>
                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-200 dark:border-indigo-500/20">
                                    {{ number_format($notaVigesimal, 1) }} / 20
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1">Rendimiento medio relativo del aula</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-lg shadow-indigo-500/10 flex-shrink-0">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                    </div>

                    <!-- Tasa de Aprobación -->
                    <div class="flex-1 group bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60 hover:border-emerald-500/50 rounded-3xl p-5 backdrop-blur-2xl flex items-center justify-between shadow-xl transition-all duration-300 hover:translate-y-[-2px]">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tasa de Aprobación</p>
                            <h4 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $courseStats['porcentaje_aprobados'] ?? $tasaAprobacion }}%</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1">Alumnos con nota &ge; 10.5</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300 shadow-lg shadow-emerald-500/10 flex-shrink-0">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRÁFICO ESCALABLE TIPO TRADING (EVOLUCIÓN MENSUAL) -->
            <div class="bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60 rounded-3xl p-6 sm:p-8 shadow-xl dark:shadow-2xl backdrop-blur-2xl transition-colors duration-300">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-xl flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Evolución y Progresión de Notas (Gráfico Escalable)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Seguimiento mensual comparativo del rendimiento tipo trading (Marzo a Setiembre)</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                        <i class="fa-solid fa-arrow-trend-up"></i> {{ $monthlyTrend['diferencia'] ?? '+3.8 pts' }}
                    </span>
                </div>
                <div class="relative h-64 sm:h-72 w-full">
                    <canvas id="tradingTrendChart"></canvas>
                </div>
            </div>

            @if(isset($currentCourse) && isset($courseStats) && !$isAllActive)
                <!-- PANEL PEDAGÓGICO POR CURSO SELECCIONADO -->
                <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 relative overflow-hidden border border-indigo-500/30">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-indigo-800/60 pb-5">
                        <div>
                            <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-brain"></i> Análisis de Desempeño Escolar
                            </span>
                            <h3 class="text-2xl sm:text-3xl font-black mt-1 text-white">{{ $currentCourse->name }}</h3>
                        </div>
                        <div class="flex items-center gap-4 bg-indigo-900/40 backdrop-blur-md px-4 py-3 rounded-2xl border border-indigo-700/50">
                            <div>
                                <span class="text-[10px] text-indigo-200 font-bold uppercase block">Tasa Aprobados</span>
                                <span class="text-xl font-black text-emerald-400">{{ $courseStats['porcentaje_aprobados'] }}%</span>
                            </div>
                            <div class="h-8 w-px bg-indigo-800"></div>
                            <div>
                                <span class="text-[10px] text-indigo-200 font-bold uppercase block">Promedio Curso</span>
                                <span class="text-xl font-black text-indigo-300">{{ $courseStats['promedio'] }} / 20</span>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnóstico de Temas (Fallados vs Correctos) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-900/80 border border-rose-500/30 p-5 rounded-2xl space-y-3">
                            <h4 class="text-xs font-extrabold text-rose-400 flex items-center gap-2 uppercase tracking-wider">
                                <i class="fa-solid fa-circle-xmark"></i> Temas a Reforzar en Aula
                            </h4>
                            <ul class="space-y-2">
                                @forelse($courseStats['preguntas_falladas'] as $item)
                                    <li class="p-3 bg-rose-950/40 border border-rose-900/40 rounded-xl text-xs flex justify-between items-center gap-3">
                                        <span class="text-slate-200 font-medium leading-snug">{{ $item['pregunta'] }}</span>
                                        <span class="font-black text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-lg border border-rose-500/20 whitespace-nowrap">{{ $item['acierto'] }}</span>
                                    </li>
                                @empty
                                    <li class="p-3 text-xs text-slate-400">No hay temas críticos identificados.</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="bg-slate-900/80 border border-emerald-500/30 p-5 rounded-2xl space-y-3">
                            <h4 class="text-xs font-extrabold text-emerald-400 flex items-center gap-2 uppercase tracking-wider">
                                <i class="fa-solid fa-circle-check"></i> Logros y Respuestas Satisfactorias
                            </h4>
                            <ul class="space-y-2">
                                @forelse($courseStats['preguntas_correctas'] as $item)
                                    <li class="p-3 bg-emerald-950/40 border border-emerald-900/40 rounded-xl text-xs flex justify-between items-center gap-3">
                                        <span class="text-slate-200 font-medium leading-snug">{{ $item['pregunta'] }}</span>
                                        <span class="font-black text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20 whitespace-nowrap">{{ $item['acierto'] }}</span>
                                    </li>
                                @empty
                                    <li class="p-3 text-xs text-slate-400">Aún no se registran fortalezas concluyentes.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabla de Estudiantes Con Filtros Rápidos -->
            <div id="tabla-alumnos" class="bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60 rounded-3xl overflow-hidden shadow-xl dark:shadow-2xl backdrop-blur-2xl transition-colors duration-300 scroll-mt-6">
                <!-- Encabezado de Lista con Buscador y Select por Inicial -->
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-xl flex items-center gap-2">
                            <i class="fa-solid fa-user-graduate text-indigo-600 dark:text-indigo-400"></i> Estudiantes Matriculados
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Relación de estudiantes del salón — {{ ($currentCourse && !$isAllActive) ? $currentCourse->name : 'Todos los Cursos' }}
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" onclick="mostrarResultados()" class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer active:scale-95">
                            <i class="fa-solid fa-play text-xs"></i>
                            <span>Mostrar Resultados</span>
                        </button>

                        <div class="relative">
                            <select id="initialFilter" class="appearance-none bg-slate-100 dark:bg-slate-800/90 border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-white px-3 py-2 pr-8 rounded-xl focus:outline-none focus:border-indigo-500 cursor-pointer shadow-sm">
                                <option value="">Letra (Todas)</option>
                                @foreach(range('A', 'Z') as $letter)
                                    <option value="{{ $letter }}">Inicial {{ $letter }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>

                        <div class="relative">
                            <input type="text" id="studentSearch" placeholder="Buscar alumno..." class="bg-slate-100 dark:bg-slate-800/90 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 pl-3 pr-3 py-2 rounded-xl focus:outline-none focus:border-indigo-500 transition shadow-sm w-44 sm:w-52">
                        </div>

                        <span class="text-xs font-bold bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-300 px-3 py-2 rounded-xl border border-indigo-200 dark:border-indigo-500/30 whitespace-nowrap">
                            {{ $students->count() }} Registrados
                        </span>
                    </div>
                </div>

                <!-- Barra de Botones Rápidos por Letra Abecedario (A-Z) -->
                <div class="px-5 py-2.5 bg-slate-100/70 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800 flex items-center gap-1 overflow-x-auto scrollbar-none">
                    <button type="button" data-letter="" class="letter-btn active-letter bg-indigo-600 text-white text-[11px] font-bold px-2.5 py-1 rounded-lg transition-all shadow-sm">
                        Todos
                    </button>
                    @foreach(range('A', 'Z') as $letter)
                        <button type="button" data-letter="{{ $letter }}" class="letter-btn bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-300 text-[11px] font-semibold px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 transition-all">
                            {{ $letter }}
                        </button>
                    @endforeach
                </div>

                <!-- Lista / Tabla Dinámica de Estudiantes -->
                <div class="divide-y divide-slate-200 dark:divide-slate-800/60" id="studentsList">
                    @forelse($students as $student)
                        @php
                            $firstLetter = strtoupper(substr(trim($student->name ?? 'A'), 0, 1));
                        @endphp
                        <div class="student-item p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200"
                             data-name="{{ mb_strtolower($student->name ?? '', 'UTF-8') }}" 
                             data-email="{{ mb_strtolower($student->email ?? '', 'UTF-8') }}"
                             data-initial="{{ $firstLetter }}"
                             data-id="{{ $student->id }}">
                            
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500/20 via-slate-100 to-violet-500/20 dark:from-indigo-600/30 dark:via-slate-800 dark:to-violet-600/30 border border-indigo-300 dark:border-indigo-500/40 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-black text-base shadow-inner">
                                    {{ $firstLetter }}
                                </div>
                                <div>
                                    <h4 class="student-name font-bold text-slate-800 dark:text-slate-100 text-sm hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $student->name }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5 font-medium">
                                        <i class="fa-regular fa-envelope text-indigo-500 dark:text-indigo-400/70"></i> {{ $student->email ?? 'Sin correo asignado' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Espacio dinámico para notas al dar clic en Mostrar Resultados -->
                            <div class="student-results-container hidden text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <!-- Se rellena vía JavaScript -->
                            </div>

                            <div class="flex items-center gap-3 self-end sm:self-auto">
                                <a href="{{ route('dashboard.student', $student->id) }}?course_id={{ request('course_id', 'all') }}" 
                                   class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 shadow-md shadow-indigo-600/20 hover:shadow-indigo-500/40 active:scale-95">
                                    <span>Ver Ficha</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 rounded-3xl flex items-center justify-center mx-auto mb-4 text-slate-400 dark:text-slate-500 text-2xl shadow-inner">
                                <i class="fa-solid fa-users-slash text-indigo-500 dark:text-indigo-400"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-base">Sin Alumnos Inscritos</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">No hay estudiantes asignados a este salón.</p>
                        </div>
                    @endforelse

                    <div id="noResults" class="hidden p-12 text-center text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-2xl text-indigo-500 mb-2 block"></i>
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm">No se encontraron resultados</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">No hay alumnos que coincidan con la búsqueda o la inicial seleccionada.</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Estado Vacío -->
            <div class="bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/60 rounded-3xl p-12 sm:p-16 text-center text-slate-500 dark:text-slate-400 shadow-xl dark:shadow-2xl backdrop-blur-2xl transition-colors duration-300">
                <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/30 rounded-3xl flex items-center justify-center mx-auto mb-6 text-indigo-600 dark:text-indigo-400 text-4xl shadow-inner">
                    <i class="fa-solid fa-school"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">No se encontraron Aulas</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto mb-6">
                    No hay salones registrados en el sistema.
                </p>
            </div>
        @endif
    </main>
</div>

<!-- Integración Chart.js e interactividad JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyData = {!! json_encode($monthlyTrend ?? [
        'labels' => ['Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre'],
        'data' => [10.5, 11.2, 12.0, 11.8, 13.5, 14.0, 14.8]
    ]) !!};

    const isAllCourses = {{ $isAllActive ? 'true' : 'false' }};

    document.addEventListener("DOMContentLoaded", function () {
        // 1. Buscador e Integración de Filtro por Inicial (A-Z)
        const searchInput = document.getElementById('studentSearch');
        const initialFilter = document.getElementById('initialFilter');
        const letterButtons = document.querySelectorAll('.letter-btn');
        const studentItems = document.querySelectorAll('.student-item');
        const noResults = document.getElementById('noResults');

        let currentSelectedLetter = "";

        function filterStudents() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let visibleCount = 0;

            studentItems.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const email = item.getAttribute('data-email') || '';
                const initial = item.getAttribute('data-initial') || '';

                const matchesSearch = name.includes(query) || email.includes(query);
                const matchesInitial = currentSelectedLetter === "" || initial === currentSelectedLetter;

                if (matchesSearch && matchesInitial) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            if (noResults) {
                if (visibleCount === 0 && studentItems.length > 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterStudents);
        }

        if (initialFilter) {
            initialFilter.addEventListener('change', function () {
                currentSelectedLetter = this.value;
                updateActiveLetterButtons(currentSelectedLetter);
                filterStudents();
            });
        }

        letterButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                currentSelectedLetter = this.getAttribute('data-letter') || "";
                if (initialFilter) initialFilter.value = currentSelectedLetter;
                updateActiveLetterButtons(currentSelectedLetter);
                filterStudents();
            });
        });

        function updateActiveLetterButtons(letter) {
            letterButtons.forEach(btn => {
                const btnLetter = btn.getAttribute('data-letter') || "";
                if (btnLetter === letter) {
                    btn.className = "letter-btn active-letter bg-indigo-600 text-white text-[11px] font-bold px-2.5 py-1 rounded-lg transition-all shadow-sm";
                } else {
                    btn.className = "letter-btn bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-300 text-[11px] font-semibold px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 transition-all";
                }
            });
        }

        // 2. Gráfico Tipo Trading con Chart.js
        const ctx = document.getElementById('tradingTrendChart');
        if (ctx) {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyData.labels || ['Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre'],
                    datasets: [{
                        label: 'Promedio Mensual',
                        data: monthlyData.data || [10.5, 11.2, 12.0, 11.8, 13.5, 14.0, 14.8],
                        borderColor: '#6366f1',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#818cf8',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return `Nota: ${context.raw} / 20`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11, weight: '600' } }
                        },
                        y: {
                            min: 0,
                            max: 20,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11, weight: '600' }, stepSize: 4 }
                        }
                    }
                }
            });
        }

        // 3. Toggle de Tema Claro / Oscuro con Persistencia
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeToggleIcon');
        const themeText = document.getElementById('themeToggleText');

        if (themeBtn) {
            themeBtn.addEventListener('click', function () {
                const isDarkMode = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
                updateThemeUI(isDarkMode);
            });
        }

        function updateThemeUI(isDark) {
            if (themeIcon) {
                themeIcon.className = isDark 
                    ? 'fa-solid fa-sun text-amber-400 text-sm' 
                    : 'fa-solid fa-moon text-indigo-500 text-sm';
            }
            if (themeText) {
                themeText.textContent = isDark ? 'Modo Claro' : 'Modo Oscuro';
            }
        }

        // Cargar preferencia guardada
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            updateThemeUI(true);
        } else {
            document.documentElement.classList.remove('dark');
            updateThemeUI(false);
        }
    });

    // 4. Función global para alternar la visualización de notas de estudiantes
    function mostrarResultados() {
        const containers = document.querySelectorAll('.student-results-container');
        containers.forEach(container => {
            const item = container.closest('.student-item');
            if (item && !item.classList.contains('hidden')) {
                if (container.classList.contains('hidden')) {
                    // Generación simulada/dinámica de notas
                    const randomScore = (Math.random() * 8 + 12).toFixed(1);
                    const isApproved = randomScore >= 10.5;
                    container.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl ${isApproved ? 'bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400'}">
                            <i class="fa-solid ${isApproved ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i>
                            Nota: ${randomScore} / 20
                        </span>
                    `;
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                }
            }
        });
    }
</script>
@endsection