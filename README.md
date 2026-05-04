# AssessHub Enterprise - Assessment Module

AssessHub Enterprise is a production-ready, high-performance assessment management system. This repository contains the core UI and logic for managing templates, question banks, and conducting proctored assessments.

## 🚀 Core Features

### 1. Assessment Pack (Creation Wizard)
A robust 4-step workflow to transition from a template to a live assessment:
- **Step 1: Select Template** - Choose base criteria and categories.
- **Step 2: Add Questions** - Supports Auto-selection, Manual Override, and Bulk CSV Upload.
- **Step 3: Assign Test** - Designate target roles or individual employees.
- **Step 4: Schedule & Publish** - Set timing and live status.

### 2. Execution View (Proctored Test Interface)
A premium, full-screen testing environment featuring:
- **Live Timer**: Dynamic countdown with warning states.
- **Question Navigator**: Visual grid showing answered, flagged, and current questions.
- **Flagging System**: Revisit difficult questions later.
- **Proctoring Simulation**: Detects violations (like tab switching) to maintain integrity.

### 3. Results & Evaluation
- **Student View**: Detailed scorecard with percentage, time taken, and topic-wise breakdown.
- **Evaluator View**: Manual marking interface for "2-Mark" and descriptive questions. Allows judges to award partial marks and submit final grades.

---

## 🛠️ Implementation Guide

### UI Structure
The project is built using **HTML5**, **CSS3 (Vanilla)**, and **Bootstrap 5**. The interface is split into a dashboard and a full-screen `execution-wrapper`.

### State Management
A global `App.executionState` object handles the lifecycle of an active test:
```javascript
executionState: {
    active: false,
    questions: [],
    currentIndex: 0,
    answers: {},
    flagged: new Set(),
    timeLeft: 3600,
    violations: 0
}
```

### Manual Marking Flow
Subjective questions (2-Mark) are filtered and rendered for the evaluator:
```javascript
const subjectiveQuestions = App.mockQuestions.filter(q => q.type === '2-Mark');
// Renders inputs for partial marks (e.g., Award 1 / 2 Marks)
```

---

## 📂 Project Structure
- `index.html`: Main dashboard and modal definitions.
- `script.js`: Application logic, state management, and event handling.
- `style.css`: Custom design system with enterprise-grade aesthetics.

## 💡 How to Use in Another Project
1. Copy the CSS variables from `:root` in `style.css`.
2. Include the `App` object and `Storage` manager from `script.js`.
3. Use the `execution-wrapper` HTML structure for a full-screen proctored experience.
