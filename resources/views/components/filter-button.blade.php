<button
    type="button"
    class="btn btn-primary btn-sm"
    data-filter-toggle="{{ $target }}">

    <i class="material-icons-outlined fs-5 me-1">filter_list</i>
    <span>Show Filter</span>

</button>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.querySelector('[data-filter-toggle="{{ $target }}"]');

    if (!btn) return;

    const target = document.getElementById('{{ $target }}');
    const text = btn.querySelector('span');

    const storageKey = 'filter_' + window.location.pathname + '_{{ $target }}';

    if (localStorage.getItem(storageKey) === 'show') {
        target.style.display = 'block';
        text.innerText = 'Hide Filter';
    } else {
        target.style.display = 'none';
        text.innerText = 'Show Filter';
    }

    btn.addEventListener('click', function () {

        if (target.style.display === 'none' || target.style.display === '') {

            target.style.display = 'block';
            text.innerText = 'Hide Filter';
            localStorage.setItem(storageKey, 'show');

        } else {

            target.style.display = 'none';
            text.innerText = 'Show Filter';
            localStorage.setItem(storageKey, 'hide');

        }

    });

});
</script>