import React, { useState } from 'react';
import { SafeAreaView, View, Text, Pressable, StyleSheet } from 'react-native';
import LoginScreen from './src/screens/LoginScreen';
import RegisterScreen from './src/screens/RegisterScreen';
import NeedsScreen from './src/screens/NeedsScreen';
import NeedDetailScreen from './src/screens/NeedDetailScreen';

export default function App() {
  const [screen, setScreen] = useState('login');
  const [selectedNeed, setSelectedNeed] = useState(null);

  return (
    <SafeAreaView style={styles.safe}>
      <View style={styles.nav}>
        <Pressable onPress={() => setScreen('login')}><Text style={styles.navItem}>Login</Text></Pressable>
        <Pressable onPress={() => setScreen('register')}><Text style={styles.navItem}>Register</Text></Pressable>
        <Pressable onPress={() => setScreen('needs')}><Text style={styles.navItem}>Needs</Text></Pressable>
      </View>

      {screen === 'login' && <LoginScreen onSuccess={() => setScreen('needs')} />}
      {screen === 'register' && <RegisterScreen />}
      {screen === 'needs' && (
        <NeedsScreen
          onSelectNeed={(need) => {
            setSelectedNeed(need);
            setScreen('need-detail');
          }}
        />
      )}
      {screen === 'need-detail' && <NeedDetailScreen need={selectedNeed} onBack={() => setScreen('needs')} />}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: '#f5f7fb' },
  nav: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 12,
    backgroundColor: '#0b4f6c'
  },
  navItem: { color: '#fff', fontWeight: '700' }
});
