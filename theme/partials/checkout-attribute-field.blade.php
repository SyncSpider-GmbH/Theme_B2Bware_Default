{{--
    One checkout custom-attribute input.

    Props:
    - $attribute: AttributesHub Attribute (order group, use_in_checkout).
    - $index: unique running index across ALL checkout attribute inputs on the
      page — feeds the attributes[{index}][attribute_id|value] field names the
      place-order request expects. Do NOT restart it per fieldset.

    Markup extracted verbatim from the checkout page's "Additional details"
    panel so collection fieldsets and the ungrouped panel render identically.
    `media` attributes must be skipped by the caller.
--}}
<div class="checkout__attribute flex flex-col gap-1">
        <input type="hidden" name="attributes[{{ $index }}][attribute_id]" value="{{ $attribute->id }}">
        @switch(strtolower((string) $attribute->type))
            @case('boolean')
                <label class="flex items-center gap-2 text-sm text-body">
                    <input type="hidden" name="attributes[{{ $index }}][value]" value="0">
                    <input type="checkbox" name="attributes[{{ $index }}][value]" value="1" @checked(old('attributes.' . $index . '.value')) class="accent-primary shrink-0">
                    <span>{{ $attribute->name }}</span>
                </label>
                @break
            @case('select')
            @case('swatch')
                <label class="flex flex-col gap-1 text-sm text-body">
                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                    <select name="attributes[{{ $index }}][value]" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                        <option value="">@t('Select an option')</option>
                        @foreach($attribute->values as $opt)
                            <option value="{{ $opt->value }}" @selected(old('attributes.' . $index . '.value') == $opt->value)>{{ $opt->value }}</option>
                        @endforeach
                    </select>
                </label>
                @break
            @case('radio')
                <span class="text-sm text-body">{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                <div class="flex flex-col gap-2">
                    @foreach($attribute->values as $opt)
                        <label class="inline-flex items-center gap-2 text-sm text-body">
                            <input type="radio" name="attributes[{{ $index }}][value]" value="{{ $opt->value }}" @checked(old('attributes.' . $index . '.value') == $opt->value) {{ $attribute->required ? 'required' : '' }} class="accent-primary shrink-0">
                            <span>{{ $opt->value }}</span>
                        </label>
                    @endforeach
                </div>
                @break
            @case('multiselect')
                <span class="text-sm text-body">{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                <div class="flex flex-col gap-2">
                    @foreach($attribute->values as $opt)
                        <label class="inline-flex items-center gap-2 text-sm text-body">
                            <input type="checkbox" name="attributes[{{ $index }}][value][]" value="{{ $opt->value }}" @checked(in_array($opt->value, (array) old('attributes.' . $index . '.value', []))) class="accent-primary shrink-0">
                            <span>{{ $opt->value }}</span>
                        </label>
                    @endforeach
                </div>
                @break
            @case('textarea')
            @case('richtext')
                <label class="flex flex-col gap-1 text-sm text-body">
                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                    <textarea name="attributes[{{ $index }}][value]" rows="3" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">{{ old('attributes.' . $index . '.value') }}</textarea>
                </label>
                @break
            @case('number')
                <label class="flex flex-col gap-1 text-sm text-body">
                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                    <input type="number" name="attributes[{{ $index }}][value]" value="{{ old('attributes.' . $index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                </label>
                @break
            @case('date')
                <label class="flex flex-col gap-1 text-sm text-body">
                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                    <input type="date" name="attributes[{{ $index }}][value]" value="{{ old('attributes.' . $index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                </label>
                @break
            @case('datetime')
                <label class="flex flex-col gap-1 text-sm text-body">
                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                    <input type="datetime-local" name="attributes[{{ $index }}][value]" value="{{ old('attributes.' . $index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                </label>
                @break
            @default
                <label class="flex flex-col gap-1 text-sm text-body">
                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                    <input type="text" name="attributes[{{ $index }}][value]" value="{{ old('attributes.' . $index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                </label>
        @endswitch
        @storefrontError('attributes.' . $index . '.value')
    </div>
