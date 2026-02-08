@php
    /** @var \App\Models\Faq|null $faq */
    /** @var \Illuminate\Support\Collection|array|null $categories */
    $categories = $categories ?? collect();

    // Checkbox: nur für diesen Organisator sichtbar?
    $onlyOrganiserOld = old('only_organiser');
    $onlyOrganiser = $onlyOrganiserOld !== null
        ? (bool) $onlyOrganiserOld
        : !empty($faq?->organisers_id);
@endphp

<div class="form-field">
    <label for="category" class="form-label">Kategorie:</label>
    <input
        type="text"
        list="faq_categories"
        id="category"
        name="category"
        maxlength="100"
        placeholder="Kategorie wählen oder neu eingeben"
        value="{{ old('category', $faq->category ?? '') }}"
        class="form-input-text w-full @if($errors->has('category')) is-invalid @endif"
    >

    <datalist id="faq_categories">
        @foreach($categories as $cat)
            <option value="{{ $cat }}"></option>
        @endforeach
    </datalist>

    @if($errors->has('category'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('category') }}</strong>
        </span>
    @endif
</div>

<div class="form-field">
    <label for="question" class="form-label">Frage:</label>
    <input
        type="text"
        id="question"
        name="question"
        value="{{ old('question', $faq->question ?? '') }}"
        class="form-input-text w-full @if($errors->has('question')) is-invalid @endif"
    >

    @if($errors->has('question'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('question') }}</strong>
        </span>
    @endif
</div>

<div class="form-field">
    <label for="answer_html" class="form-label">Antwort (HTML):</label>
    <textarea
        id="answer_html"
        name="answer_html"
        rows="10"
        class="form-input-textarea @if($errors->has('answer_html')) is-invalid @endif"
    >{{ old('answer_html', $faq->answer_html ?? '') }}</textarea>

    @if($errors->has('answer_html'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('answer_html') }}</strong>
        </span>
    @endif
</div>

<div class="form-field">
    <label class="form-label">Sichtbarkeit:</label>

    <label for="only_organiser" class="inline-flex items-center">
        <input
            type="checkbox"
            id="only_organiser"
            name="only_organiser"
            value="1"
            class="form-input-text"
            {{ $onlyOrganiser ? 'checked' : '' }}
        >
        <span class="ml-2">Nur für diesen Organisator sichtbar</span>
    </label>

    <p class="text" style="margin-top: 6px;">
        Wenn nicht gesetzt, ist der FAQ-Eintrag global (für alle Organisatoren).
    </p>
</div>

<div class="form-field">
    <label for="is_active" class="form-label">Aktiv:</label>
    <input
        type="checkbox"
        id="is_active"
        name="is_active"
        value="1"
        class="form-input-text @if($errors->has('is_active')) is-invalid @endif"
        {{ old('is_active', ($faq->is_active ?? true)) ? 'checked' : '' }}
    >

    @if($errors->has('is_active'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('is_active') }}</strong>
        </span>
    @endif
</div>
