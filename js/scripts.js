const { createApp, ref, computed, onMounted, watch } = Vue;

createApp({
  setup() {
    const products = ref([]);
    const cart = ref([]);
    const isCartVisible = ref(false);
    const selectedFaction = ref('all');
    const selectedCategory = ref('all');

    const fetchProducts = async () => {
      const params = new URLSearchParams({
        faction: selectedFaction.value,
        category: selectedCategory.value,
      });
      const url = `api_get_products.php?${params.toString()}`;

      try {
        const response = await fetch(url);
        if (!response.ok) throw new Error("Network response was not ok.");
        const data = await response.json();
        products.value = data;
      } catch (error) {
        console.error("Не вдалося завантажити товари:", error);
        products.value = [];
      }
    };

    onMounted(() => {
      fetchProducts();
      const savedCart = localStorage.getItem('warhammerShopCart');
      if (savedCart) {
        cart.value = JSON.parse(savedCart);
      }
    });

    watch([selectedFaction, selectedCategory], fetchProducts);

    watch(cart, (newCart) => {
      localStorage.setItem('warhammerShopCart', JSON.stringify(newCart));
    }, { deep: true });

    const cartItemCount = computed(() => {
      return cart.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    const cartTotal = computed(() => {
      return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    });

    const showNotification = (message) => {
      const notificationElement = document.getElementById('cart-notification');
      if (notificationElement) {
        notificationElement.textContent = message;
        notificationElement.style.display = 'block';
        setTimeout(() => {
          notificationElement.style.display = 'none';
        }, 3000);
      }
    };
    
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

    const openCart = () => { isCartVisible.value = true; };
    const closeCart = () => { isCartVisible.value = false; };

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
      closeCart,
      selectedFaction,
      selectedCategory,
    };
  }
}).mount('#vue-cart-app');