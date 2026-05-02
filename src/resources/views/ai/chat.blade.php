@extends('layouts.app')

@section('title', __('app.ai_title'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">{{ __('app.ai_title') }}</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">{{ __('app.ai_chat') }}</h3>
                            <button
                                id="ai-clear-btn"
                                type="button"
                                class="btn btn-sm btn-danger"
                            >
                                <i class="fas fa-trash mr-1"></i> {{ __('app.ai_clear') }}
                            </button>
                        </div>

                        <div
                            id="ai-chat"
                            data-ask-url="{{ $askUrl }}"
                            data-clear-url="{{ $clearUrl }}"
                        >
                            <div class="card-body" id="ai-chat-box" style="height:500px; overflow-y:auto;">
                                @forelse($history as $item)
                                    <div class="d-flex {{ $item['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                                        <div class="p-3 rounded {{ $item['role'] === 'user' ? 'bg-primary text-white' : 'bg-light text-dark' }}" style="max-width:75%">
                                            <p class="mb-0" style="white-space: pre-line">{{ $item['content'] }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p id="ai-empty-state" class="text-muted text-center mt-5">
                                        <i class="fas fa-comments fa-2x mb-2 d-block"></i>
                                        {{ __('app.ai_empty_state') }}
                                    </p>
                                @endforelse
                            </div>

                            <div class="card-footer">
                                <div id="ai-error" class="alert alert-danger mb-2 d-none"></div>
                                <form id="ai-chat-form" class="d-flex gap-2">
                                    <input
                                        id="ai-message-input"
                                        type="text"
                                        name="message"
                                        maxlength="1500"
                                        class="form-control"
                                        placeholder="{{ __('app.ai_placeholder') }}"
                                    >
                                    <button id="ai-send-btn" type="submit" class="btn btn-primary ml-2">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection