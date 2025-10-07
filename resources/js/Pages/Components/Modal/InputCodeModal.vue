<script setup>
import { ref, nextTick, onMounted, onUnmounted, watch } from 'vue';

const emit = defineEmits(['closeModal', 'submit', 'resendCode']);
const props = defineProps({
  code: {
    type: String,
    default: ''
  }
});

const inputRefs = ref([]);
const codeDigits = ref(['', '', '', '', '', '']);
const resendCooldown = ref(0);
const cooldownInterval = ref(null);

const focusInput = (index) => {
  nextTick(() => {
    if (inputRefs.value[index]) {
      inputRefs.value[index].focus();
    }
  });
};

const handleInput = (event, index) => {
  const value = event.target.value;

  // Only allow numbers
  if (!/^\d*$/.test(value)) {
    codeDigits.value[index] = '';
    return;
  }

  // If user pasted multiple digits, distribute them
  if (value.length > 1) {
    const digits = value.split('').slice(0, 6);
    digits.forEach((digit, i) => {
      if (index + i < 6) {
        codeDigits.value[index + i] = digit;
      }
    });

    // Focus the last input that got a value
    const lastFilledIndex = Math.min(index + digits.length - 1, 5);
    focusInput(lastFilledIndex);
    return;
  }

  codeDigits.value[index] = value;

  // Auto-focus next input if current input has a value
  if (value && index < 5) {
    focusInput(index + 1);
  }

  emitCode();
};

const handleBackspace = (event, index) => {
  if (event.key === 'Backspace' && !codeDigits.value[index] && index > 0) {
    // If current input is empty and backspace is pressed, focus previous input
    codeDigits.value[index - 1] = '';
    focusInput(index - 1);
  }
  emitCode();
};

const handleKeyDown = (event, index) => {
  // Allow: backspace, delete, tab, escape, enter, arrow keys, and numbers
  if ([8, 9, 27, 13, 37, 38, 39, 40, 46].includes(event.keyCode) ||
      // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
      (event.ctrlKey && [65, 67, 86, 88].includes(event.keyCode)) ||
      // Allow: numbers
      (event.keyCode >= 48 && event.keyCode <= 57) ||
      (event.keyCode >= 96 && event.keyCode <= 105)) {
    return;
  }
  event.preventDefault();
};

const emitCode = () => {
  const code = codeDigits.value.join('');
  emit('update:code', code);
};

const setInputRef = (el, index) => {
  inputRefs.value[index] = el;
};

const handleResendCode = () => {
  if (resendCooldown.value > 0) return;

  // Start cooldown (60 seconds)
  resendCooldown.value = 60;
  startCooldown();
  emit('resendCode');
};

const startCooldown = () => {
  // Clear any existing interval first
  if (cooldownInterval.value) {
    clearInterval(cooldownInterval.value);
  }

  cooldownInterval.value = setInterval(() => {
    if (resendCooldown.value > 0) {
      resendCooldown.value--;
    } else {
      clearInterval(cooldownInterval.value);
      cooldownInterval.value = null;
    }
  }, 1000);
};

// Auto-focus first input when modal opens
onMounted(() => {
  focusInput(0);
  // Start with 60 seconds cooldown when modal opens
  resendCooldown.value = 60;
  startCooldown();
});

onUnmounted(() => {
  if (cooldownInterval.value) {
    clearInterval(cooldownInterval.value);
    cooldownInterval.value = null;
  }
});
</script>

<template>
  <div class="fixed inset-0 z-50 grid place-items-center backdrop-brightness-50">
    <div class="relative rounded-lg bg-white p-6 shadow-lg w-[350px]">
      <!-- ❌ Close button -->
      <button
        @click="emit('closeModal')"
        class="absolute right-4 top-4 text-gray-500 hover:text-gray-800 text-lg"
      >
        ✕
      </button>

      <!-- Modal content -->
      <div class="text-center">
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Enter verification code</h2>
        <p class="text-gray-600 mb-6">We've sent a 6-digit code to your email</p>

        <!-- Code input boxes -->
        <div class="flex justify-center gap-2 mb-6">
          <div
            v-for="(digit, index) in codeDigits"
            :key="index"
            class="relative"
          >
            <input
              :ref="(el) => setInputRef(el, index)"
              v-model="codeDigits[index]"
              @input="handleInput($event, index)"
              @keydown="handleKeyDown($event, index)"
              @keyup="handleBackspace($event, index)"
              type="text"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="6"
              class="w-12 h-12 text-center text-xl font-semibold border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none transition-colors"
            />
          </div>
        </div>

        <!-- Submit button -->
        <button
          @click="$emit('submit')"
          class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg font-medium transition-colors disabled:bg-orange-300 disabled:cursor-not-allowed mb-4"
          :disabled="codeDigits.join('').length !== 6"
        >
          Verify Code
        </button>

        <!-- Resend code option -->
        <p class="text-gray-600 text-sm">
          Didn't receive the code?
          <button
            @click="handleResendCode"
            class="text-orange-500 hover:text-orange-600 font-medium ml-1 transition-colors disabled:text-orange-300 disabled:cursor-not-allowed"
            :disabled="resendCooldown > 0"
          >
            {{ resendCooldown > 0 ? `Resend in ${resendCooldown}s` : 'Resend' }}
          </button>
        </p>
      </div>
    </div>
  </div>
</template>
