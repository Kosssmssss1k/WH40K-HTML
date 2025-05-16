const { createApp, ref, computed, onMounted, watch } = Vue;

createApp({
  setup() {
    const products = ref([
        { id: 'rogal_dorn', name: 'Танк Рогал Дорн', price: 2500, image: './images/miniatures/imperial guard/vehicle/RogalDornBattleTank.jpg' },
        { id: 'fulgrim_transfigured', name: 'Фулгрім', price: 1800, image: './images/miniatures/primarch/FulgrimTransfigured.jpg' },
        { id: 'lions_retinue', name: 'Ліон', price: 3200, image: './images/miniatures/primarch/LionsRetinue.jpg' }
    ]);
    const cart = ref([]); 
    const isCartVisible = ref(false);

    onMounted(() => {
      const savedCart = localStorage.getItem('warhammerShopCart');
      if (savedCart) {
        cart.value = JSON.parse(savedCart);
      }
    });

    watch(cart, (newCart) => {
      localStorage.setItem('warhammerShopCart', JSON.stringify(newCart));
    }, { deep: true }); 

    const cartItemCount = computed(() => {
      return cart.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    const cartTotal = computed(() => {
      return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    });

    const addToCart = (product) => {
      const existingItem = cart.value.find(item => item.id === product.id);
      if (existingItem) {
        existingItem.quantity++;
      } else {
        cart.value.push({ ...product, quantity: 1 });
      }
      showNotification(`${product.name} додано до кошика!`);
    };

    const removeFromCart = (productId) => {
      cart.value = cart.value.filter(item => item.id !== productId);
    };

    const updateQuantity = (productId, newQuantityValue) => {
      const numQuantity = parseInt(newQuantityValue, 10);
      const itemInCart = cart.value.find(item => item.id === productId);

      if (itemInCart) {
        if (numQuantity > 0) {
          itemInCart.quantity = numQuantity;
        } else {
          removeFromCart(productId);
        }
      }
    };

    const openCart = () => {
      isCartVisible.value = true;
    };

    const closeCart = () => {
      isCartVisible.value = false;
    };
    
    const showNotification = (message) => {
        const notificationElement = document.getElementById('cart-notification');
        if (notificationElement) {
            notificationElement.textContent = message;
            notificationElement.style.display = 'block';

            setTimeout(() => {
                notificationElement.style.display = 'none';
            }, 3000);
        } else {
            console.warn("Елемент #cart-notification не знайдено на сторінці.");
        }
    };

    return {
      products,
      cart,
      isCartVisible,
      cartItemCount,
      cartTotal,
      addToCart,
      removeFromCart,
      updateQuantity,
      openCart,
      closeCart
    };
  }
}).mount('#vue-cart-app'); // Монтуємо додаток до div#vue-cart-app