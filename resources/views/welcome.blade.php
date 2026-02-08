@extends('layouts.guest')

@section('content')
<div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 selection:bg-red-500 selection:text-white">
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
        <div class="flex justify-center">
            <h1 class="text-6xl font-bold text-gray-900">
                {{ config('app.name', 'KV-ERP-CRM-SaaS') }}
            </h1>
        </div>

        <div class="mt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            Multi-Tenant Architecture
                        </h2>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Built with database-per-tenant isolation for maximum security and data separation.
                        </p>
                    </div>
                </div>

                <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            Modular Design
                        </h2>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Clean architecture with domain-driven design for scalable enterprise solutions.
                        </p>
                    </div>
                </div>

                <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            Laravel 10 + Vue.js 3
                        </h2>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Modern tech stack with Laravel 10, Vue.js 3, and Tailwind CSS.
                        </p>
                    </div>
                </div>

                <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            Enterprise Ready
                        </h2>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Production-ready with authentication, authorization, and multi-organization support.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">
            <div class="text-center text-sm text-gray-500 sm:text-left">
                <div class="flex items-center gap-4">
                    <a href="https://github.com/kasunvimarshana/kv-erp-crm-saas" class="group inline-flex items-center hover:text-gray-700 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                        GitHub Repository
                    </a>
                </div>
            </div>

            <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
            </div>
        </div>
    </div>
</div>
@endsection
