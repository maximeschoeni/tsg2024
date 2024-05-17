<header id="header">
  <div class="menu-button">
    <button onclick="document.body.classList.toggle('menu-open')">
      <!-- <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
        <path d="M11.3858 0.582052L6.67643 5.29138L2.09438 0.709331L0.43975 2.36396L5.0218 6.94601L0.312471 11.6553L2.09438 13.4373L6.80371 8.72792L11.3858 13.31L13.0404 11.6553L8.45834 7.07329L13.1677 2.36396L11.3858 0.582052Z" fill="black"/>
      </svg> -->
      ☰
    </button>
  </div>
</header>
<nav id="menu" class="menu">
  <a class="close" onclick="document.body.classList.remove('menu-open')">
    <!-- <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
      <path d="M11.3858 0.582052L6.67643 5.29138L2.09438 0.709331L0.43975 2.36396L5.0218 6.94601L0.312471 11.6553L2.09438 13.4373L6.80371 8.72792L11.3858 13.31L13.0404 11.6553L8.45834 7.07329L13.1677 2.36396L11.3858 0.582052Z" fill="black"/>
    </svg> -->
    ✕
  </a>

  <?php wp_nav_menu(array(
    'theme_location' => 'top_menu',
    'container' => false,
    'menu_class' => 'top-menu',
    'items_wrap' => '<ul id="%1$s" class="%2$s"><li><a href="'.home_url().'">'.__('Accueil', 'tsg').'</a></li><li><a>'.__('Taille du texte', 'tsg').'</a><div class="font-size"><button class="small">+</button><button class="big">+</button></div></li>%3$s</ul>'


    // 'walker' => new Actwall_Walker_Nav_Menu()
  )) ?>
</nav>
