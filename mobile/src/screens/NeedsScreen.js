import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, Pressable, StyleSheet } from 'react-native';
import { apiRequest } from '../api/client';

export default function NeedsScreen({ onSelectNeed }) {
  const [needs, setNeeds] = useState([]);
  const [error, setError] = useState('');

  useEffect(() => {
    apiRequest('/needs')
      .then((data) => setNeeds(Array.isArray(data) ? data : data.data || []))
      .catch((e) => setError(e.message));
  }, []);

  return (
    <View style={styles.wrap}>
      <Text style={styles.title}>Needs</Text>
      {!!error && <Text style={styles.error}>{error}</Text>}
      <FlatList
        data={needs}
        keyExtractor={(item) => String(item.id)}
        renderItem={({ item }) => (
          <Pressable style={styles.card} onPress={() => onSelectNeed?.(item)}>
            <Text style={styles.type}>{item.type}</Text>
            <Text>Qty: {item.quantity}</Text>
            <Text>Status: {item.status}</Text>
          </Pressable>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { padding: 16, flex: 1 },
  title: { fontSize: 22, fontWeight: '700', marginBottom: 12 },
  card: { backgroundColor: '#fff', borderRadius: 10, padding: 12, marginBottom: 10, borderWidth: 1, borderColor: '#e5e7eb' },
  type: { fontWeight: '700', marginBottom: 5 },
  error: { color: '#b02a37', marginBottom: 8 }
});
