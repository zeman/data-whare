<div class="nav">
    <div>
        <span class="logo">DataWhare</span>
        <ul>
            <li @if ($nav == 'sun') class="active" @endif>
                <a href="/sun">
                    <div class="nav__icon">
                        <svg width="15" height="15">
                            <use href="/img/icons.svg#sun" class="icon"/>
                        </svg>
                    </div>
                    <div>Sun</div>
                </a>
            </li>
            <li @if ($nav == 'water') class="active" @endif>
                <a href="/water">
                    <div class="nav__icon">
                        <svg width="15" height="15">
                            <use href="/img/icons.svg#water" class="icon"/>
                        </svg>
                    </div>
                    <div>Water</div>
                </a>
            </li>
        </ul>
    </div>
    <div class="nav__right">
        <a href="/settings">
            <svg width="20" height="20">
                <use href="/img/icons.svg#cog" class="icon"/>
            </svg>
        </a>
    </div>
</div>
