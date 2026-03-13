import { ref } from 'vue';

export function useClipboard(timeout = 2000) {
  const isCopied = ref(false);

  const copy = async (text: string | null | undefined) => {
    if (!text) return false;

    if (navigator.clipboard && window.isSecureContext) {
      try {
        await navigator.clipboard.writeText(text);
        return handleSuccess();
      } catch (err) {
        console.warn('Modern clipboard API failed, trying fallback...', err);
      }
    }

    return fallbackCopy(text);
  };

  const handleSuccess = () => {
    isCopied.value = true;
    setTimeout(() => (isCopied.value = false), timeout);
    return true;
  };

  const fallbackCopy = (text: string) => {
    try {
      const textArea = document.createElement('textarea');
      textArea.value = text;

      textArea.style.position = 'fixed';
      textArea.style.left = '-9999px';
      textArea.style.top = '0';
      document.body.appendChild(textArea);

      textArea.focus();
      textArea.select();

      const successful = document.execCommand('copy');
      document.body.removeChild(textArea);

      if (successful) return handleSuccess();
      return false;
    } catch (err) {
      console.error('Fallback copy failed:', err);
      return false;
    }
  };

  return {
    isCopied,
    copy,
  };
}
