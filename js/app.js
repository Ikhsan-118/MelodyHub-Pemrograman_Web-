/* js/app.js - MelodyHub player + modal + collection (separate file) */
document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  /* ---------- data ---------- */
  const tracks = [
    { id:'Sectumsempra', title:'Sectumsempra', artist:'.Feast', src:'assets/audio/Sectumsempra.mp3', cover:'assets/img/Multiverse.jpeg', duration:'4:09'},
    { id:"Doves, '25 on Blank Canvas", title:"Doves, '25 on Blank Canvas", artist:'Hindia', src:'assets/audio/everything u are.mp3', cover:'assets/img/Doves,25onBlankCanvas.jpeg', duration:'3:56'},
  ];

  const LS_KEYS = { PLAYLISTS: 'mh_playlists_v1', LIKES: 'mh_likes_v1', QUEUE: 'mh_queue_v1', VOLUME: 'mh_volume_v1' };

  /* ---------- helpers ---------- */
  const $ = (s, root=document) => root.querySelector(s);
  const $$ = (s, root=document) => Array.from(root.querySelectorAll(s));
  const formatTime = (sec) => {
    if(!sec && sec !== 0) return '0:00';
    const s = Math.floor(sec % 60), m = Math.floor((sec/60)%60);
    return m + ':' + (s<10?'0'+s:s);
  };

  /* ---------- modal system ---------- */
  const overlay = $('#modal-overlay');
  const modalBody = $('#modal-body');
  const modalTitle = $('#modal-title');
  const modalClose = $('#modal-close');

  function openModal(name, title='') {
    modalBody.innerHTML = '';
    modalTitle.textContent = title || name;
    if(name === 'browse') renderBrowse(modalBody);
    else if(name === 'search') renderSearchModal(modalBody);
    else if(name === 'playlist') renderPlaylistModal(modalBody);
    else if(name === 'artist') renderArtistModal(modalBody);
    overlay.classList.add('active');
    overlay.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    overlay.classList.remove('active');
    overlay.setAttribute('aria-hidden','true');
    modalBody.innerHTML = '';
    document.body.style.overflow = '';
  }
  modalClose.addEventListener('click', closeModal);
  overlay.addEventListener('click', (e) => { if(e.target === overlay) closeModal(); });
  document.addEventListener('keydown', (e)=> { if(e.key === 'Escape' && overlay.classList.contains('active')) closeModal(); });

  // bind nav buttons to open modal
  $$('.nav-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const name = btn.dataset.modal;
      if(name === 'home') { closeModal(); window.scrollTo({top:0, behavior:'smooth'}); return; }
      openModal(name, btn.textContent.trim());
      // active state
      $$('.nav-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  /* ---------- render modal content ---------- */
  function renderBrowse(root) {
    const html = `
      <section>
        <h3>Browse — Genre & Rilisan</h3>
        <p class="small">Jelajahi genre populer dan rilisan terbaru.</p>
        <div class="cards-row" style="margin-top:12px;">
          <article class="card"><figure><img src="assets/img/genre-pop.jpg" alt="Indie"></figure><h3>Indie</h3><p class="small">Rileks & melodi</p></article>
          <article class="card"><figure><img src="assets/img/genre-alt.jpg" alt="Alternatif"></figure><h3>Alternatif</h3><p class="small">Energi panggung</p></article>
        </div>
      </section>`;
    root.innerHTML = html;
    // allow clicking cards to close modal and play first track (example)
    $$('.card', root).forEach((c, i) => c.addEventListener('click', ()=> { loadTrack(i % tracks.length); play(); closeModal(); }));
  }

  function renderSearchModal(root) {
    root.innerHTML = `
      <section>
        <h3>Cari</h3>
        <p class="small">Cari lagu atau artis</p>
        <div style="margin-top:8px"><input id="modal-search-input" type="search" placeholder="Ketik..." style="width:100%;padding:8px;border-radius:8px;background:transparent;border:1px solid rgba(255,255,255,0.04);color:inherit"></div>
        <div id="modal-search-results" class="grid" style="margin-top:12px"></div>
      </section>`;
    const input = $('#modal-search-input');
    const results = $('#modal-search-results');
    function doSearch(q) {
      const term = q.trim().toLowerCase();
      results.innerHTML = '';
      if(!term) { results.innerHTML = '<p class="small">Ketik untuk mencari...</p>'; return; }
      const found = tracks.filter(t => t.title.toLowerCase().includes(term) || t.artist.toLowerCase().includes(term));
      if(found.length === 0) { results.innerHTML = '<p class="small">Tidak ditemukan</p>'; return; }
      found.forEach(t => {
        const art = document.createElement('article');
        art.className = 'album';
        art.innerHTML = `<div class="cover"><img src="${t.cover}" alt="${t.title}"></div><h4>${t.title}</h4><p class="small">${t.artist}</p>`;
        art.addEventListener('click', ()=> { loadById(t.id); play(); closeModal(); });
        results.appendChild(art);
      });
    }
    input.addEventListener('input', ()=> doSearch(input.value));
    input.focus();
  }

  function renderPlaylistModal(root) {
    root.innerHTML = `<section><h3>Playlist Saya</h3><div id="modal-playlists"></div></section>`;
    const block = $('#modal-playlists');
    const raw = localStorage.getItem(LS_KEYS.PLAYLISTS);
    const pls = raw ? JSON.parse(raw) : {};
    if(Object.keys(pls).length === 0) { block.innerHTML = '<p class="small">Belum ada playlist. Buat menggunakan tombol Buat di Koleksi Kamu.</p>'; return; }
    for(const name in pls) {
      const item = document.createElement('article');
      item.className = 'card';
      item.innerHTML = `<h4>${name}</h4><p class="small">${pls[name].length} lagu</p>`;
      block.appendChild(item);
      // click to view content (simple)
      item.addEventListener('click', () => {
        const list = pls[name];
        modalBody.innerHTML = `<header style="margin-bottom:10px"><h3>${name}</h3><button class="close" id="modal-back"><i class="fas fa-arrow-left"></i></button></header><div id="pl-list"></div>`;
        $('#modal-back').addEventListener('click', ()=> renderPlaylistModal(root));
        const plList = $('#pl-list');
        list.forEach(id => {
          const t = tracks.find(x => x.id === id);
          if(t) {
            const el = document.createElement('article'); el.className='album';
            el.innerHTML = `<div class="cover"><img src="${t.cover}" alt="${t.title}"></div><h4>${t.title}</h4><p class="small">${t.artist}</p>`;
            el.addEventListener('click', ()=> { loadById(t.id); play(); closeModal(); });
            plList.appendChild(el);
          }
        });
      });
    }
  }

  function renderArtistModal(root) {
    root.innerHTML = `<section><h3>Artis</h3><p class="small">Daftar artis pilihan.</p><div class="grid" style="margin-top:8px">
      <article class="card"><figure><img src="assets/img/secerca-cahaya.jpg" alt="Hindia"></figure><h3>Hindia</h3><p class="small">Penyanyi-penulis lagu</p></article>
      <article class="card"><figure><img src="assets/img/senja-rindu.jpg" alt=".feast"></figure><h3>.feast</h3><p class="small">Proyek indie</p></article>
    </div></section>`;
    $$('.card', root).forEach((c, i) => c.addEventListener('click', ()=> { /* optionally open artist detail */ }));
  }

  /* ---------- collection (Koleksi Kamu) ---------- */
  const collectionList = $('#collection-list');
  const collectionTabs = $$('.tab');
  const createBtn = $('#create-playlist');
  const collectionSearch = $('#collection-search');

  function loadPlaylists() {
    const raw = localStorage.getItem(LS_KEYS.PLAYLISTS);
    return raw ? JSON.parse(raw) : {};
  }
  function savePlaylists(obj) { localStorage.setItem(LS_KEYS.PLAYLISTS, JSON.stringify(obj)); }

  function renderCollection(tab = 'playlist', filter = '') {
    collectionList.innerHTML = '';
    filter = (filter||'').toLowerCase();
    if(tab === 'playlist') {
      const pls = loadPlaylists();
      if(Object.keys(pls).length === 0) {
        collectionList.innerHTML = '<p class="small">Belum ada playlist — klik Buat untuk menambahkan.</p>';
        return;
      }
      for(const name in pls) {
        if(filter && !name.toLowerCase().includes(filter)) continue;
        const li = document.createElement('div'); li.className='collection-item';
        li.innerHTML = `<img src="assets/img/playlist-kalcer.jpg" alt=""><div><strong>${name}</strong><div class="small">${pls[name].length} lagu</div></div>`;
        li.addEventListener('click', ()=> { openModal('playlist', name); });
        collectionList.appendChild(li);
      }
    } else if(tab === 'artis') {
      const artists = ['Hindia','.feast','LombaSihir','Barasuara','The Adams','The Jansen'];
      artists.filter(a => a.toLowerCase().includes(filter)).forEach(a => {
        const d = document.createElement('div'); d.className='collection-item';
        d.innerHTML = `<img src="assets/img/secerca-cahaya.jpg" alt="${a}"><div><strong>${a}</strong><div class="small">Artis</div></div>`;
        d.addEventListener('click', ()=> openModal('artist', a));
        collectionList.appendChild(d);
      });
    } else if(tab === 'album') {
      const albums = ['Matahari','Single','Album Demo'];
      albums.filter(a => a.toLowerCase().includes(filter)).forEach(a => {
        const d = document.createElement('div'); d.className='collection-item';
        d.innerHTML = `<img src="assets/img/peluru.jpg" alt="${a}"><div><strong>${a}</strong><div class="small">Album</div></div>`;
        collectionList.appendChild(d);
      });
    } else if(tab === 'podcast') {
      const p = ['Podcast Kalcer','Podcast Indie'];
      p.filter(x => x.toLowerCase().includes(filter)).forEach(a => {
        const d = document.createElement('div'); d.className='collection-item';
        d.innerHTML = `<img src="assets/img/cover-dailymix1.jpg" alt="${a}"><div><strong>${a}</strong><div class="small">Podcast</div></div>`;
        collectionList.appendChild(d);
      });
    }
  }

  // tab clicks
  collectionTabs.forEach(t => t.addEventListener('click', () => {
    collectionTabs.forEach(x => x.classList.remove('active'));
    t.classList.add('active');
    const tabName = t.dataset.tab;
    renderCollection(tabName, collectionSearch.value);
  }));

  // create playlist prompt
  createBtn.addEventListener('click', () => {
    const name = prompt('Nama playlist baru:');
    if(!name) return;
    const pls = loadPlaylists();
    if(pls[name]) { alert('Nama playlist sudah ada'); return; }
    // create empty
    pls[name] = [];
    savePlaylists(pls);
    renderCollection('playlist');
    // open playlist modal
    openModal('playlist', name);
  });

  // collection search
  collectionSearch.addEventListener('input', () => {
    const active = $('.tab.active').dataset.tab;
    renderCollection(active, collectionSearch.value);
  });

  // initial render
  renderCollection('playlist');

  /* ---------- Player ---------- */
  const audio = new Audio(); audio.preload = 'metadata';
  let currentIndex = 0, isPlaying=false, isShuffle=false, repeatMode='none';
  const playerThumb = $('#player-thumb'), playerTitle = $('#player-title'), playerArtist = $('#player-artist');
  const playBtn = $('.play-toggle'), prevBtn = $('.prev'), nextBtn = $('.next'), shuffleBtn = $('.shuffle'), repeatBtn = $('.repeat');
  const seekFill = $('.seek > i'), seekBar = $('.seek'), timeCur = $('.time-current'), timeTot = $('.time-total');

  function loadTrack(i) {
    if(i < 0 || i >= tracks.length) return;
    currentIndex = i;
    const t = tracks[i];
    audio.src = t.src;
    if(playerThumb) playerThumb.src = t.cover || 'assets/img/1.jpeg';
    if(playerTitle) playerTitle.textContent = t.title;
    if(playerArtist) playerArtist.textContent = t.artist;
    if(timeTot) timeTot.textContent = t.duration || '0:00';
    document.title = `${t.title} — MelodyHub`;
  }
  function loadById(id){ const idx = tracks.findIndex(t=>t.id===id); if(idx>=0) loadTrack(idx); }

  function play(){ audio.play().then(()=>{ isPlaying=true; updatePlayUI(); }).catch(()=>{}); }
  function pause(){ audio.pause(); isPlaying=false; updatePlayUI(); }
  function togglePlay(){ isPlaying? pause() : play(); }
  function prevTrack(){ if(audio.currentTime > 4) { audio.currentTime = 0; return; } if(isShuffle) currentIndex = Math.floor(Math.random()*tracks.length); else currentIndex = (currentIndex -1 + tracks.length)%tracks.length; loadTrack(currentIndex); play(); }
  function nextTrack(){ if(isShuffle) currentIndex = Math.floor(Math.random()*tracks.length); else currentIndex = (currentIndex +1)%tracks.length; loadTrack(currentIndex); play(); }
  function updatePlayUI(){ if(playBtn) playBtn.textContent = isPlaying ? '⏸' : '▶'; if(shuffleBtn) shuffleBtn.classList.toggle('active', isShuffle); if(repeatBtn) repeatBtn.classList.toggle('active', repeatMode!=='none'); }

  // event bindings
  if(playBtn) playBtn.addEventListener('click', togglePlay);
  if(prevBtn) prevBtn.addEventListener('click', prevTrack);
  if(nextBtn) nextBtn.addEventListener('click', nextTrack);
  if(shuffleBtn) shuffleBtn.addEventListener('click', ()=> { isShuffle = !isShuffle; updatePlayUI(); });
  if(repeatBtn) repeatBtn.addEventListener('click', ()=> { repeatMode = repeatMode==='none' ? 'all' : (repeatMode==='all' ? 'one' : 'none'); repeatBtn.dataset.mode = repeatMode; updatePlayUI(); });

  // progress
  let progressTimer = null;
  function startProgress(){ stopProgress(); progressTimer = setInterval(()=> { if(!audio.duration) return; const pct = (audio.currentTime/audio.duration)*100; if(seekFill) seekFill.style.width = pct + '%'; if(timeCur) timeCur.textContent = formatTime(audio.currentTime); }, 200); }
  function stopProgress(){ if(progressTimer) { clearInterval(progressTimer); progressTimer=null; } }

  if(seekBar) {
    seekBar.addEventListener('click', (e)=> {
      if(!audio.duration) return;
      const rect = seekBar.getBoundingClientRect();
      const x = e.clientX - rect.left;
      audio.currentTime = Math.max(0, Math.min(1, x/rect.width)) * audio.duration;
    });
    // drag support
    let dragging = false;
    function moveSeek(e) {
      if(!dragging) return;
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const rect = seekBar.getBoundingClientRect();
      audio.currentTime = Math.max(0, Math.min(1, (clientX-rect.left)/rect.width)) * audio.duration;
    }
    seekBar.addEventListener('mousedown', ()=> { dragging=true; stopProgress(); document.addEventListener('mousemove', moveSeek); });
    document.addEventListener('mouseup', ()=> { if(dragging) { dragging=false; startProgress(); document.removeEventListener('mousemove', moveSeek); }});
    seekBar.addEventListener('touchstart', ()=> { dragging=true; stopProgress(); document.addEventListener('touchmove', moveSeek); });
    document.addEventListener('touchend', ()=> { if(dragging){ dragging=false; startProgress(); document.removeEventListener('touchmove', moveSeek); }});
  }

  audio.addEventListener('loadedmetadata', ()=> { if(timeTot) timeTot.textContent = formatTime(audio.duration); });
  audio.addEventListener('timeupdate', ()=> { if(timeCur) timeCur.textContent = formatTime(audio.currentTime); if(seekFill && audio.duration) seekFill.style.width = (audio.currentTime/audio.duration*100) + '%'; });
  audio.addEventListener('play', ()=> { isPlaying=true; updatePlayUI(); startProgress(); });
  audio.addEventListener('pause', ()=> { isPlaying=false; updatePlayUI(); stopProgress(); });
  audio.addEventListener('ended', ()=> {
    // queue first
    const q = JSON.parse(localStorage.getItem(LS_KEYS.QUEUE) || '[]');
    if(q.length) {
      const nextId = q.shift(); localStorage.setItem(LS_KEYS.QUEUE, JSON.stringify(q));
      const idx = tracks.findIndex(t => t.id === nextId);
      if(idx >= 0) { loadTrack(idx); play(); return; }
    }
    if(repeatMode === 'one') { audio.currentTime = 0; play(); return; }
    if(isShuffle) { currentIndex = Math.floor(Math.random()*tracks.length); loadTrack(currentIndex); play(); return; }
    if(currentIndex === tracks.length -1) { if(repeatMode === 'all') { loadTrack(0); play(); } else pause(); } else nextTrack();
  });

  audio.addEventListener('error', () => { console.warn('Audio error', audio.src); nextTrack(); });

  // volume control injection
  (function injectVolume(){
    const prog = document.querySelector('.progress');
    if(!prog) return;
    const wrapper = document.createElement('div'); wrapper.className = 'volume-control';
    wrapper.innerHTML = '<input class="volume-slider" type="range" min="0" max="1" step="0.01" aria-label="Volume">';
    prog.parentNode.insertBefore(wrapper, prog.nextSibling);
    const slider = wrapper.querySelector('.volume-slider');
    const saved = parseFloat(localStorage.getItem(LS_KEYS.VOLUME));
    if(!isNaN(saved)) { audio.volume = saved; slider.value = saved; } else { audio.volume = 0.9; slider.value = 0.9; }
    slider.addEventListener('input', ()=> { audio.volume = parseFloat(slider.value); localStorage.setItem(LS_KEYS.VOLUME, audio.volume); });
  })();

  /* ---------- overlays (like, queue) and click-to-play ---------- */
  function injectOverlays() {
    const items = $$('[data-track]');
    items.forEach((el) => {
      if(el.querySelector('.like-btn')) return;
      el.style.position = el.style.position || 'relative';
      const trackId = el.dataset.track;
      // like
      const like = document.createElement('button'); like.className='like-btn'; like.innerHTML = '<i class="fas fa-heart"></i>'; like.title='Suka';
      const likes = new Set(JSON.parse(localStorage.getItem(LS_KEYS.LIKES) || '[]'));
      if(likes.has(trackId)) like.classList.add('liked');
      like.addEventListener('click', (ev)=> { ev.stopPropagation(); const s = new Set(JSON.parse(localStorage.getItem(LS_KEYS.LIKES) || '[]')); if(s.has(trackId)){ s.delete(trackId); like.classList.remove('liked'); } else { s.add(trackId); like.classList.add('liked'); } localStorage.setItem(LS_KEYS.LIKES, JSON.stringify(Array.from(s))); });
      el.appendChild(like);
      // queue
      const qbtn = document.createElement('button'); qbtn.className='queue-btn'; qbtn.innerHTML = '<i class="fas fa-plus"></i>'; qbtn.title='Tambahkan ke antrean';
      qbtn.addEventListener('click', (ev)=> { ev.stopPropagation(); const q = JSON.parse(localStorage.getItem(LS_KEYS.QUEUE) || '[]'); q.push(trackId); localStorage.setItem(LS_KEYS.QUEUE, JSON.stringify(q)); qbtn.classList.add('added'); setTimeout(()=>qbtn.classList.remove('added'), 800); });
      el.appendChild(qbtn);
      // click to play
      el.addEventListener('click', ()=> { const idx = tracks.findIndex(t => t.id === trackId); if(idx >= 0) { loadTrack(idx); play(); } });
    });
  }

  // initial injection
  injectOverlays();

  // attach cards in reco row and main grid
  $$('#reco-row .card, #main-grid .album').forEach(el => {
    const track = el.dataset.track;
    if(track) el.style.cursor = 'pointer';
  });

  // main search input filters main grid in real time
  const mainSearch = $('#main-search');
  if(mainSearch) mainSearch.addEventListener('input', () => {
    const q = mainSearch.value.trim().toLowerCase();
    $$('#main-grid .album').forEach(a => {
      const txt = (a.textContent || '').toLowerCase();
      a.style.display = txt.includes(q) ? '' : 'none';
    });
  });

  // collection initial load & updates
  renderCollection('playlist');

  // click overlay nav links that had data-modal attribute (already bound above)

  /* ---------- init play first track ---------- */
  loadTrack(0);
});
