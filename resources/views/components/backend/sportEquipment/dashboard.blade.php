<div class="dashboard-flexbox">
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Sportgeräte von {{ $organiser->veranstaltung }} bearbeiten</h2>
                <a class="dasboard-iconbox-a" href="{{ route('backend.sportEquipment.index') }}">
                    <box-icon name='edit'></box-icon>
                </a>
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Verwaltung aller Sportgeräte</h2>
            <a class="dasboard-iconbox-a" href="{{ route('backend.sportEquipment.indexAll') }}">
                <box-icon name='edit'></box-icon>
            </a>
        </div>
    </div>
</div>
