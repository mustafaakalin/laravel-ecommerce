<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">🛒🛍️💳✅👉&nbsp;Ödeme&nbsp;👈🛒💸💰👀</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - Address & Payment -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Shipping Address Section -->
        <div class="card bg-base-100 shadow-xl">
          <div class="card-body">
            <div v-if="userAddresses.length === 0" class="flex flex-col items-center justify-center p-8">
              <h2 class="text-xl font-semibold mb-4">Adres Bulunamadı!</h2>
              <p class="text-gray-600 mb-4">Ödeme işlemine devam etmek için lütfen bir teslimat adresi ekleyin.</p>
              <a :href="route('filament.admin.resources.addresses.create')" class="btn btn-primary">
                Yeni Adres Ekle
              </a>
            </div>
            <div v-else-if="userAddresses.length === 1">
              <div class="space-y-4">
                <h2 class="text-xl font-semibold">🚚📦✈️🚢🚀🚛👉&nbsp;Teslimat Adresi&nbsp;👈✨🌟</h2>
                <div class="p-4 bg-base-200 rounded-lg space-y-2">
                  <p class="font-medium">{{ userAddresses[0].title }}</p>
                  <p>{{ userAddresses[0].address }}</p>
                  <p>{{ userAddresses[0].city }}, {{ userAddresses[0].state }} {{ userAddresses[0].zip_code }}</p>
                  <p>{{ userAddresses[0].country }}</p>
                  <p>Phone: {{ userAddresses[0].phone }}</p>
                </div>
              </div>
            </div>
            <div v-else class="space-y-4">
              <h2 class="text-xl font-semibold">🚚📦✈️🚢🚀🚛👉&nbsp;Teslimat Adresi Seçiniz&nbsp;👈✨🌟</h2>
              <select v-model="selectedAddress" class="select select-bordered w-full">
                <option value="">Bir adres seçin</option>
                <option v-for="address in userAddresses" :key="address.id" :value="address.id"
                  :selected="address.is_default">
                  {{ address.title }} - {{ address.city }}, {{ address.state }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div v-if="userAddresses.length > 0">
          <!-- Payment Section -->
          <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
              <h2 class="text-xl font-semibold mb-6">Ödeme Detayları</h2>

              <!-- Payment Method Selection -->
              <div class="mb-6">
                <select v-model="paymentMethod" class="select select-bordered w-full">
                  <option value="iyzico">iyzico</option>
                </select>
              </div>

              <!-- Credit Card Inputs -->
              <div class="space-y-4">
                <div class="form-control">
                  <input type="text" v-model="cardName" placeholder="Kart Sahibi Adı" class="input input-bordered w-full">
                  <span v-if="cardNameError" class="text-error text-sm mt-1">{{ cardNameError }}</span>
                </div>

                <div class="form-control">
                  <input type="number" v-model="cardNumber" placeholder="Kart Numarası" class="input input-bordered w-full"
                    title="iyzico test kartı: 5890040000000016">
                  <span v-if="cardNumberError" class="text-error text-sm mt-1">{{ cardNumberError }}</span>
                </div>

                <div class="grid grid-cols-3 gap-4">
                  <div class="form-control">
                    <input type="number" v-model="expireMonth" placeholder="MM (Ay)" class="input input-bordered w-full">
                    <span v-if="expireMonthError" class="text-error text-sm mt-1">{{ expireMonthError }}</span>
                  </div>
                  <div class="form-control">
                    <input type="number" v-model="expireYear" placeholder="YY (Yıl)" class="input input-bordered w-full">
                    <span v-if="expireYearError" class="text-error text-sm mt-1">{{ expireYearError }}</span>
                  </div>
                  <div class="form-control">
                    <input type="number" v-model="cvc" placeholder="CVC" class="input input-bordered w-full">
                    <span v-if="cvcError" class="text-error text-sm mt-1">{{ cvcError }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="userAddresses.length > 0" class="lg:col-span-1">
        <div class="card bg-gradient-to-t from-primary/10 to-secondary/10 backdrop-blur-2xl  shadow-xl sticky top-4">
          <div class="card-body">
            <h2 class="text-xl font-semibold mb-4">Sipariş Özeti</h2>

            <!-- Cart Items -->
            <div class="divide-y divide-base-300">
              <div v-for="item in cart.items" :key="item.id" class="py-3">
                <div class="flex flex-col space-y-2">
                  <!-- Product Name and Base Price -->
                  <div class="flex justify-between">
                    <span class="font-medium">{{ item.product.name }} × {{ item.quantity }}</span>
                    <span class="line-through text-gray-500">
                      {{ formatPrice(item.getOriginalPrice() * item.quantity) }} ₺
                    </span>
                  </div>

                  <!-- Product Discount -->
                  <div v-if="item.product.discount > 0" class="flex justify-between text-sm text-success">
                    <span>Ürün İndirimi (-{{ item.product.discount }}%)</span>
                    <span>
                      {{ formatPrice(item.getOriginalPrice() * item.quantity * (item.product.discount / 100)) }} ₺
                    </span>
                  </div>

                  <!-- Campaign Discount -->
                  <div v-if="item.campaign()" class="flex justify-between text-sm text-success">
                    <span>{{ item.campaign().name }}
                      <span v-if="item.campaign().discount_type === 'percentage'">
                        (-{{ item.campaign().discount_value }}%)
                      </span>
                      <span v-else>
                        (-{{ formatPrice(item.campaign().discount_value) }} ₺)
                      </span>
                    </span>
                    <span>
                      <span v-if="item.campaign().discount_type === 'percentage'">
                        {{ formatPrice(item.getOriginalPrice() * item.quantity * (item.campaign().discount_value / 100)) }}
                        ₺
                      </span>
                      <span v-else>
                        {{ formatPrice(item.campaign().discount_value * item.quantity) }} ₺
                      </span>
                    </span>
                  </div>

                  <!-- Final Price -->
                  <div class="flex justify-between font-semibold text-primary">
                    <span>Son Fiyat</span>
                    <span>{{ formatPrice(item.getTotalPrice()) }} ₺</span>
                  </div>

                  <!-- Total Savings -->
                  <div v-if="item.getDiscountPercentage() > 0" class="text-xs text-success">
                    Toplam tasarruf: {{ item.getDiscountPercentage() }}%
                    ({{ formatPrice((item.getOriginalPrice() - item.getDiscountedPrice()) * item.quantity) }} ₺)
                  </div>
                </div>
              </div>
            </div>

            <!-- Totals Summary -->
            <div class="mt-6 space-y-3 border-t pt-4">
              <div class="flex justify-between">
                <span>Orijinal Toplam</span>
                <span class="line-through text-gray-500">
                  {{ formatPrice(cart.items.reduce((sum, item) => sum + item.getOriginalPrice() * item.quantity, 0)) }}
                  ₺
                </span>
              </div>

              <!-- Total Product Discounts -->
              <div v-if="totalProductDiscounts > 0" class="flex justify-between text-success">
                <span>Ürün İndirimleri</span>
                <span>-{{ formatPrice(totalProductDiscounts) }} ₺</span>
              </div>

              <div v-if="shipmentPrice > 0" class="flex justify-between text-gray-500">
                <span>Kargo Fiyatı {{ shipmentDiscountPrice }} ₺ ve üzeri kargo bedava </span>
                <span>{{ formatPrice(shipmentPrice) }} ₺</span>
              </div>

              <!-- Total Campaign Discounts -->
              <div v-if="totalCampaignDiscounts > 0" class="flex justify-between text-success">
                <span>Kampanya İndirimleri</span>
                <span>-{{ formatPrice(totalCampaignDiscounts) }} ₺</span>
              </div>

              <!-- Coupon Discount -->
              <div v-if="discount > 0" class="flex justify-between text-success">
                <span>Kupon İndirimi</span>
                <span>-{{ formatPrice(discount) }} ₺</span>
              </div>

              <!-- Final Total -->
              <div class="flex justify-between font-bold text-lg pt-2 border-t">
                <span>Nihai Toplam</span>
                <span>{{ formatPrice(totalPrice - discount) }} ₺</span>
              </div>

              <!-- Total Savings -->
              <div class="text-success text-sm">
                Toplam Tasarruf:
                {{ formatPrice(
                  cart.items.reduce((sum, item) => sum + (item.getOriginalPrice() - item.getDiscountedPrice()) * item.quantity, 0) + discount
                ) }} ₺
              </div>
            </div>

            <!-- Coupon Section -->
            <div class="mt-4">
              <div class="join w-full">
                <input type="text" v-model="couponCode" placeholder="Kupon kodunu girin"
                  class="input input-bordered join-item w-2/3">
                <button @click="applyCoupon" class="btn btn-primary join-item w-1/3">
                  Uygula
                </button>
              </div>
            </div>

            <!-- Place Order Button -->
            <button @click="placeOrder" :disabled="loading" class="btn btn-primary w-full mt-6">
              <span v-if="loading">
                İşleniyor...
              </span>
              <span v-else>
                Sipariş Ver
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      cart: { items: [] },
      couponCode: '',
      discount: 0,
      totalPrice: 0,
      paymentMethod: 'iyzico',
      userAddresses: [],
      selectedAddress: '',
      cardName: '',
      cardNumber: '',
      expireMonth: '',
      expireYear: '',
      cvc: '',
      loading: false,
      cardNameError: null,
      cardNumberError: null,
      expireMonthError: null,
      expireYearError: null,
      cvcError: null,
      shipmentPrice: 0,
      shipmentDiscountPrice: 0,
    };
  },
  mounted() {
    this.fetchData();
  },
  computed: {
    totalProductDiscounts() {
      return this.cart.items.reduce((sum, item) => {
        return sum + (item.product.discount > 0
          ? item.getOriginalPrice() * item.quantity * (item.product.discount / 100)
          : 0);
      }, 0);
    },
    totalCampaignDiscounts() {
      return this.cart.items.reduce((sum, item) => {
        const campaign = item.campaign();
        if (!campaign) return sum;

        return sum + (campaign.discount_type === 'percentage'
          ? item.getOriginalPrice() * item.quantity * (campaign.discount_value / 100)
          : campaign.discount_value * item.quantity);
      }, 0);
    },
  },
  watch: {
    cart: {
      handler() {
        this.calculateTotalPrice();
      },
      deep: true
    },
  },
  methods: {
    async fetchData() {
      try {
        const userResponse = await axios.get('/api/user');
        const user = userResponse.data;

        const cartResponse = await axios.get('/api/cart');
        this.cart = cartResponse.data;

        const addressesResponse = await axios.get('/api/user/addresses');
        this.userAddresses = addressesResponse.data;
        this.selectedAddress = this.userAddresses.find(address => address.is_default)?.id || '';

        const settingsResponse = await axios.get('/api/settings');
        this.shipmentPrice = settingsResponse.data.site_shipment_price;
        this.shipmentDiscountPrice = settingsResponse.data.shipment_discount_price;

        this.calculateTotalPrice();
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },
    calculateTotalPrice() {
      let totalPrice = 0;
      for (const item of this.cart.items) {
        totalPrice += item.getTotalPrice();
      }
      this.totalPrice = totalPrice;
    },
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    async applyCoupon() {
      try {
        const response = await axios.post('/api/coupon/apply', { coupon_code: this.couponCode });
        this.discount = response.data.discount;
        this.totalPrice = response.data.totalPrice;
        this.showToast('Kupon başarıyla uygulandı.', 'success');
      } catch (error) {
        this.showToast('Geçersiz kupon kodu.', 'error');
      }
    },
    async placeOrder() {
      this.loading = true;
      try {
        const response = await axios.post('/api/order/place', {
          paymentMethod: this.paymentMethod,
          selectedAddress: this.selectedAddress,
          cardName: this.cardName,
          cardNumber: this.cardNumber,
          expireMonth: this.expireMonth,
          expireYear: this.expireYear,
          cvc: this.cvc,
          discount: this.discount,
        });

        if (response.data.success) {
          this.showToast('Sipariş başarıyla verildi.', 'success');
          window.location.href = `/orders/success/${response.data.orderId}`;
        } else {
          this.showToast('Sipariş verilirken bir hata oluştu.', 'error');
        }
      } catch (error) {
        this.showToast('Sipariş verilirken bir hata oluştu.', 'error');
        console.error('Error placing order:', error);
      } finally {
        this.loading = false;
      }
    },
    showToast(message, type) {
      // Implement your toast notification logic here
      alert(`${type.toUpperCase()}: ${message}`);
    },
    route(name, params = {}) {
      // Implement your route generation logic here
      return `/route/${name}`;
    },
  },
};
</script>