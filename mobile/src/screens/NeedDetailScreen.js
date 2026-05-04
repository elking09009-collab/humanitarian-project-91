import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';

export default function NeedDetailScreen({ need, onBack }) {
  if (!need) {
    return (
      <View style={styles.wrap}>
        <Text>No need selected.</Text>
        <Pressable style={styles.btn} onPress={onBack}><Text style={styles.btnText}>Back</Text></Pressable>
      </View>
    );
  }

  return (
    <View style={styles.wrap}>
      <Text style={styles.title}>Need Detail #{need.id}</Text>
      <Text style={styles.line}>Type: {need.type}</Text>
      <Text style={styles.line}>Quantity: {need.quantity}</Text>
      <Text style={styles.line}>Status: {need.status}</Text>
      <Text style={styles.line}>Notes: {need.notes || '-'}</Text>
      <Pressable style={styles.btn} onPress={onBack}><Text style={styles.btnText}>Back to List</Text></Pressable>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { padding: 16 },
  title: { fontSize: 22, fontWeight: '700', marginBottom: 12 },
  line: { marginBottom: 6 },
  btn: { marginTop: 12, backgroundColor: '#0b4f6c', borderRadius: 8, padding: 10, alignItems: 'center' },
  btnText: { color: '#fff', fontWeight: '700' }
});
