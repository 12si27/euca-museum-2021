<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/2021"><i class="fas fa-landmark"></i> 애유갤 박물관 <small>2021</small> <span style="font-size: x-small;">v0.2 beta</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 150px;">
                <li class="nav-item">
                <a class="nav-link <?=($mode=='main'?'active" aria-current="page':'')?>" href="/2021">메인</a>
                </li>
                <li class="nav-item">
                <a class="nav-link <?=($mode=='stat'?'active" aria-current="page':'')?>" href="/2021/stat">통계</a>
                </li>
                <li class="nav-item">
                <a class="nav-link <?=($mode=='info'?'active" aria-current="page':'')?>" href="/2021/info">정보</a>
                </li>
                </ul>
        </div>
    </div>
</nav>