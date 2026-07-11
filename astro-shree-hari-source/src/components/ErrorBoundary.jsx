import { Component } from "react";

export default class ErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError() {
    return { hasError: true };
  }

  componentDidCatch(error, info) {
    console.error("React Error:", error, info);
    try {
      fetch("/backend/api/log-frontend-error.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          message: error.message,
          stack: error.stack,
          componentStack: info.componentStack,
          url: location.href,
          userAgent: navigator.userAgent,
        }),
      });
    } catch (e) {
      // silently fail
    }
  }

  render() {
    if (this.state.hasError) {
      return (
        <div style={{ padding: "2rem", textAlign: "center", fontFamily: "system-ui" }}>
          <h2>केही गडबड भयो</h2>
          <p>कृपया पृष्ठ रिफ्रेस गर्नुहोस् वा पछि फेरि प्रयास गर्नुहोस्।</p>
          <button onClick={() => location.reload()}>
            पृष्ठ रिफ्रेस गर्नुहोस्
          </button>
        </div>
      );
    }
    return this.props.children;
  }
}
