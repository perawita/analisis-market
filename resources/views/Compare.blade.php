<div class="card-header d-flex justify-content-between align-items-center">
    <span>{{ $compareTitle }}</span>
    @if ($compareButton)
        <button class="btn btn-primary" id="request-compare">Compare</button>
    @endif
</div>
<div class="card-body">
    <div class="table-responsive">
        <div class="d-flex flex-row flex-nowrap" style="flex-basis: 33.33%;">
            @foreach ($compare as $item)
                <div class="col-sm-6 col-lg-4 mb-4" style="flex: 0 0 auto;">
                    <div class="card p-3">
                        <figure class="p-3 mb-0">
                            @if ($compareButton)
                                <div class="form-check">
                                    <input class="form-check-input compare-checkbox" type="checkbox"
                                        value="{{ $item['symbol'] }}" id="compare-{{ $item['symbol'] }}"
                                        @if (strtolower($item['symbol']) === strtolower($symbol)) checked disabled @endif>
                                    <label class="form-check-label" for="compare-{{ $item['symbol'] }}">
                                        Compare
                                    </label>
                                </div>
                            @endif
                            <blockquote class="blockquote">
                                <p>
                                    <a
                                        href="{{ route('summary', ['Symbol' => $item['ticker']]) }}">{{ $item['ticker'] }}</a>
                                </p>
                            </blockquote>
                            <figcaption class="blockquote-footer mb-0 text-muted">
                                {{ Str::limit($item['companyName'], 30) }}
                            </figcaption>
                        </figure>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.compare-checkbox');
        const buttonCompare = document.getElementById('request-compare');

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checkedCheckboxes = document.querySelectorAll(
                    '.compare-checkbox:checked');
                if (checkedCheckboxes.length > 4) {
                    this.checked = false;
                }
            });
        });

        buttonCompare.addEventListener('click', function() {
            const selectedSymbols = [];
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    selectedSymbols.push(checkbox.value);
                }
            });

            let url = `/quote/compare/{{ $symbol }}`;
            if (selectedSymbols.length > 1) {
                const comps = selectedSymbols.filter(symbol => symbol.toLowerCase() !==
                    '{{ $symbol }}'.toLowerCase()).join(',');
                url = `/quote/compare/{{ $symbol }}?comps=${comps}`;
            }
            window.location.href = url;

        });
    });
</script>
